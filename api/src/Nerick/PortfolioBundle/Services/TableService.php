<?php
namespace Nerick\PortfolioBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Description of TableService
 *
 * @author Luwdo
 */
class TableService extends ContainerAware{
    
    private $filter = null;
    private $queryBuilder = null;
    private $serializeData = null;
    private $qbAlias = null;
    
    public function __construct() {
	$this->setFilter(function($qb){ return true; });
	$this->setSerializeData(function($results){ return array(); });
    }
    
    public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
    }
    
    public function setFilter(callable $filter){
	$this->filter = $filter;
    }
    
    public function setQueryBuilder($queryBuilder, $qbAlias){
	$this->queryBuilder = $queryBuilder;
	$this->qbAlias = $qbAlias;
    }
    
    public function setSerializeData(callable $serializeData){
	$this->serializeData = $serializeData;
    }
    
    public function getTable($request){	
	$columns = $request->get('columns');
	$draw = $request->get('draw');
	$limit = $request->get('length');
	$start = $request->get('start');
	$order = $request->get('order');
	$search = $request->get('search');
	
	$qb = $this->queryBuilder;

	//get unfiltered count
	$countQb = clone $qb;
	$totalRecords = $countQb->select('COUNT('.$this->qbAlias.')')->getQuery()->getSingleScalarResult();
	
	$this->filter($qb);
	
	//--SEARCH--
	$searchs = array();
	
	if($search && $search['value'] != ''){
	    $qb->setParameter('gobalSearch', '%'.$search['value'].'%');
	    foreach($columns as $index => $column){
		if($column['searchable'] == 'true'){
		    $columnName = $column['data'];
		    $searchs[] = $this->qbAlias.'.'.$columnName.' LIKE :gobalSearch';
		}
	    }
	    //andWhere(new Expr\Orx($aLike));
	    $gString = '( '.implode(' OR ', $searchs).' )';
	    
	    $qb->andWhere($gString);
	}
	
	//get filtered count
	$countQb = clone $qb;
	$filteredRecords = $countQb->select('COUNT('.$this->qbAlias.')')->getQuery()->getSingleScalarResult();
	
	//these are ands
//	// Individual column filtering
//	for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
//		$requestColumn = $request['columns'][$i];
//		$columnIdx = array_search( $requestColumn['data'], $dtColumns );
//		$column = $columns[ $columnIdx ];
//		$str = $requestColumn['search']['value'];
//		if ( $requestColumn['searchable'] == 'true' &&
//		 $str != '' ) {
//			$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
//			$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
//		}
//	}
	
	
	//---LIMIT---
	if ($start && $limit != -1) {
	    $qb->setFirstResult($start)->setMaxResults($limit);
	}
	//--ORDER--
	foreach($order as $index => $columnOrder){
	    $columnIndex = intval($columnOrder['column']);
	    if($columns[$columnIndex]['orderable'] == 'true'){
		$columnName = $columns[$columnIndex]['data'];
		if($columnOrder['dir'] === 'asc'){
		   $qb->orderBy($this->qbAlias.'.'.$columnName, 'ASC');
		}else{
		   $qb->orderBy($this->qbAlias.'.'.$columnName, 'DESC');
		}
	    }
	}
	
	$query = $qb->getQuery();
	$results = $query->getResult();
	
	return array(
		"draw" => intval($draw),
		"recordsTotal" => intval($totalRecords),
		"recordsFiltered" => intval($filteredRecords),
		"data" => $this->serializeData($results)
	);
    }
    
    
}
