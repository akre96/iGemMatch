<?php
App::uses('AppController', 'Controller');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    	$this->Auth->allow('add', 'logout');
    }
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$contact = $this->Auth->User('contact');
		$contacted = 0;
		if($contact)
		{
			$contacts= explode(',',$contact);
			foreach ($contacts as $team) {
			if ($team == strval($id))
			{
				$contacted = 1;
			}
		}
		}

		if ($contacted == 0)
		{
			$newContact = $contact.','.strval($id);
			$this->set('new',$newContact);
			if ($this->request->is(array('post','put')))
			{
				$userId = $this->Auth->User('id');
				$data = array('id' => $userId, 'contact' => $newContact);
				$this->set('data',$data);
				$this->User->save($data);

				$contacted = 1;
			}
		}
		$this->set('contacted',$contacted);
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		}
		$categories = $this->User->Category->find('list');
		$keywords = $this->User->Keyword->find('list');
		$this->set(compact('categories', 'keywords'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit() {
		$id = $this->Auth->User('id');
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$categories = $this->User->Category->find('list');
		$keywords = $this->User->Keyword->find('list');
		$this->set(compact('categories', 'keywords'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete()) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
    public function login() {
         
        //if already logged-in, redirect
        if($this->Session->check('Auth.User')){
            $this->redirect(array('action' => 'index','controller'=>'categories'));      
        }
         
        // if we get the post information, try to authenticate
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirectUrl());
            } 
            else {
                $this->Session->setFlash('Username or Password Incorrect.');
            }
        } 
    }
 
    public function logout() {
        $this->redirect($this->Auth->logout());
    }

}
