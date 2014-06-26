<?php

use Nette\IOException;

/**
 * Homepage/Welcome presenter
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class HomepagePresenter extends BasePresenter
{

	public function actionDefault() {
		try{
			$dao = $this->getService('companyDAO');
			$this->template->tablesPresent = $dao->tableExists();
		}catch( \PDOException $e ){
			$this->setView('databaseError');
			$this->template->error = array( 'label'=>'PDOException', 'message' => $e->getMessage() );
			$this->template->params = $this->context->params;
		}
	}
	
	public function actionInitDatabase() {
		$filler = new RandomDatabaseFiller($this->context);
		try{
			$filler->createTables();
			$this->flashSuccess('Database was initialized');
		}catch( IOException $e ) {
			$this->flashError('Error during initialization : '.$e->getMessage());
		}catch( \PDOException $e ) {
			$this->flashError('Error during initialization : '.$e->getMessage());
		}
		$this->redirect('Homepage:default');
	}
	
	public function actionRefillDatabase() {
		$filler = new RandomDatabaseFiller($this->context);
		try{
			$filler();
			$this->flashSuccess('Database was randomized');
		}catch( \PDOException $e ) {
			$this->flashError('Error during randomizing database : '.$e->getMessage());
		}
		$this->redirect('Homepage:default');
	}
	
	public function renderXxx2() {
		/* @var $em \Doctrine\ORM\EntityManager */
//		$em = $this->getService('entityManager');
//		$repo = $em->getRepository('model\database\entity\Company');
		/* @var $repo \Doctrine\ORM\EntityRepository */
//		$res = $repo->findAll();
		
		/* @var $dao model\database\dao\CompanyDAO */
		$dao = $this->getService('companyDAO');
		/* @var $ds model\database\datasource\DoctrineDataSource */
		$ds = $dao->getDataSource();
		
		$conditions = array(
			new Langosh\component\dataGrid\filter\condition\doctrine\In('name', array('This','ThisA'))
		);
		$ds->filter($conditions);
		
		$result = $ds->getData();
		
		echo '<pre>';
		\Doctrine\Common\Util\Debug::dump($result);
		echo '</pre>';
		exit;
		
//		$ds->f
		
		
		$a = array(
			array('id'=>5,'name'=>'Hubert'),
			array('id'=>1,'name'=>'Herbert'),
			array('id'=>3,'name'=>'Dean'),
			array('id'=>2,'name'=>'Gilbert'),
		);
		
		usort($a, function($a,$b){
			return $a['name']>$b['name'];
//			return $a['id']>$b['id'];
		});
		
		echo '<pre>';
		print_r($a);
		echo '</pre>';
		exit;
		
		echo '<pre>';
		doctrineDump($res);
		echo '</pre>';
		exit;
		$p = new Nette\Utils\Paginator();
		var_dump($em->getConfiguration());
		echo 'xxx';
		exit;
		
	}
	
//	public function createComponentCopmaniesGrid() {
//		$filter = new Filter();
//		
//		$dao = $this->getService($name);
//		$dataSource = $dao->getDataSource();
//		
//		$paginator = new Paginator();
//		
//		$grid = new FilterDataGrid();
//		
//		return $grid;
//	}
//	


}
