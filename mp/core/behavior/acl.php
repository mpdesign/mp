<?php
/**
 +------------------------------------------------------------------------------
 * Access Control List factory class.
 *
 * Permissions system.
 *
 * PHP versions 4 and 5
 *
 * Redistributions of files must retain the above copyright notice.
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Acl extends Mp {


	function __construct() {
		
	}

/**========================================用户权限leaf===========================================  **/
	
	//检查权限是否可访问
	function check($module = "", $action = "*") {
		
	}

	


	//管理组权限继承
	function inherit($aro, $aco, $action = "*") {
		
		return $this->allow($aro, $aco, $action);
		
	}

	//授权用户$user_id 用户组权限$acl_id
	function allow($user_id = 0, $acl_id = 0) {
		
		return $this->db->save("users", array('acls' => $acl_id), array('id' => $user_id));
		
	}
	
	
	function find($table = '', $id = 0){
		return $this->db->find_by_id($table, $id);
	}
	


/**========================================模块基础操作权限leaf===========================================  **/
	
	//添加模块基础操作 $data = array(module`,`action`,`alias`,`is_show`);
	function Add($parent_id = 0, $data = array()){
		if (empty($data))return false;
		return $this->tree->table("acl_leaf")->appendNode($parent_id, $data);
	}
	
	//编辑模块基础操作
	function Modify($id = 0, $data = array()){
		if ($id < 1)return false;
		if (empty($data))return false;
		return $this->db->save("acl_leaf", $data, array('id' => $id));
	}
	
	//删除模块基础操作
	function Delete($id = 0){
		if ($id < 1)return false;
		return $this->tree->table("acl_leaf")->removeNode($id);
	}

/**========================================用户组权限tree===========================================  **/
	
	//添加节点
	function append($parent_id = 0, $alias = ''){
		return $this->tree->table("acl_node")->appendNode($parent_id, array('alias' => $alias));
	}
	
	//授权用户组$parent_id基础权限$id
	function grant($parent_id = 0, $id = 0, $name = ''){
		//if ($id < 1)return false;
		$node = $this->db->find_by_id("acl_node", $parent_id);
		if (!empty($node) && $node["leaf_id"] < 1){
			return false;
		}
		$data['parent_id'] = $parent_id;
		$data['leaf_id'] = $id;
		$data['alias'] = $name;
		
		return $this->tree->table("acl_node")->appendNode($parent_id, $data);
	}
	
	//编辑用户组名称
	function Alias($id = 0, $name = ''){
		if ($id < 1)return false;
		return $this->db->save("acl_node", array('alias' => $name), array('id' => $id));
	}
	
	//取消用户组授权$id
	function deny($id = 0){
		if ($id < 1)return false;
		return $this->tree->table("acl_node")->removeNode($id);
	}
	
	//转移用户组$id到另一个用户下$parent_id
	function move($id = 0, $parent_id = 0){
		
		if ($id < 1 || $parent_id < 0)return false;
		return $this->tree->table("acl_node")->moveTree($id, $parent_id);
		
	}
	
	//对比已授权的ID
	function diffGranted($id = 0, $ids = array()){
		if ($id < 1)return false;
		$this->db->find("acl_node", array( 'parent_id ' => $id, 'leaf_id > ' => 0), '', '*', 'lft asc' );
		//删除旧的
		//添加新的
		//重叠的不做操作
	}
}


