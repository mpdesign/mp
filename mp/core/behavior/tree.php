<?php
/**
 +------------------------------------------------------------------------------
 * Tree model class. 
 * 
 * Enables a model object to act as a node-based tree.
 *
 * Redistributions of files must retain the above copyright notice.
 *
 * Enables a model object to act as a node-based tree. Using Modified Preorder Tree Traversal
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */
class Tree extends Mp {

	public $table_name = "tree";
	public $db_name = "default";
	
	function table($name = "tree", $db = 'default'){
		$this->table_name = $name;
		$this->db_name = $db;
		return $this;
	}

	/**
	 * get node detail
	 */
	function node($id=0){
		$node = array();
		$id = intval($id);
		if($id > 0){
			$node = load('db', 'storage')->db($this->db_name)->find_by_id($this->table_name, $id);
		}
		return $node;
	}

	/**
	 * is child of parent node
	 */
	function isChild($id=0, $parent_id=0){
		if($id < 1 || $parent_id < 1)return false;
		$node = $this->node($id);
		$parent = $this->node($parent_id);
		if($node['lft'] > $parent["lft"] and $node["rght"] < $parent["rght"]){
			return true;
		}
		return false;
	}

	/**
	 * get top node
	 *
	 * @param int $id The ID of the record to read or false to read all top level nodes
	 * @return root node
	 * @access public
	 */
	function topNode($id=0){
		if($id<1)return array();
		$node = $this->node($id);
		if(empty($node))return array();
		return load('db', 'storage')->db($this->db_name)->find($this->table_name, array("lft < " => $node["lft"], "rght > " => $node["rght"]), '1', "*", 'lft asc', 'one');
	}

/**
 * Get the number of child nodes
 *
 * @param int $id The ID of the record to read or false to read all top level nodes
 * @param bool $toplevel Just count top 1 children
 * @return integer number of child nodes
 * @access public
 
 */
	function childCount($id = 0, $toplevel = FALSE) {
		if ($id <= 0) return 0;
		if ($toplevel){
			$count = load('db', 'storage')->db($this->db_name)->find_by_field($this->table_name, array("parent_id" => $id), " count(*) as count ");
		}else{
			$node = $this->node($id);
			if (empty($node))return 0;
			$count = load('db', 'storage')->db($this->db_name)->find_by_field($this->table_name, array("lft > " => $node["lft"], "rght < " => $node["rght"]), " count(*) as count ");
		}
		return !empty($count['count']) ? $count['count'] : 0;
	}

/**
 * Get the child nodes
 *
 * @param int $id The ID of the record to read or false to read all top level nodes
 * @param bool $toplevel Just get top 1 children
 * @return array of child nodes
 * @access public
 
 */
	function children($id = 0, $toplevel = FALSE) {
		if ($id < 0) return array();
		if ($toplevel){
			$children = load('db', 'storage')->db($this->db_name)->find($this->table_name, array("parent_id" => $id), '', '*', 'lft asc');
		}else{
			if ($id == 0){
				return load('db', 'storage')->db($this->db_name)->find($this->table_name, array(), '', '*', 'lft asc');
			}
			$node = $this->node($id);
			if (empty($node))return array();
			$children = load('db', 'storage')->db($this->db_name)->find($this->table_name, array("lft > " => $node["lft"], "rght < " => $node["rght"]), '', '*', 'lft asc');
		}
		return $children;
	}

/**
 * A convenience method for returning a hierarchical array used for HTML select boxes
 *
 * @param int $id Belonging to the ID of the tree structure
 * @param string $spacer The character or characters which will be append
 * @return array An associative array of records, where the id is the key, and the display field is the value
 * @access public
 */
	function treeList($id = 0, $spacer = '_') {
		$results = $this->children($id);
		$tmp = $stack = array();
		if (empty($results))return false;
		foreach ($results as $result) {
			
			$tmp = $result;
			$level = 0;
			if ($stack){	
				foreach ($stack as $s) {
					if ($s["lft"] <= $result["lft"] && $s["rght"] >= $result["rght"]){
						$level++;
					}
				}
			}
			$tmp['prefix'] = str_repeat($spacer, $level);
			$tmp['level'] = $level;
			$stack[] = $tmp;
		}
		return $stack;
	}
	

/**
 * Get the parent node
 *
 * reads the parent id and returns this node
 *
 * @param int $id The ID of the record to read
 * @return array Array of data for the parent node
 * @access public
 */
	function parentNode($id = 0) {
		$id = intval($id);
		if ($id<1)return array();
		$node = $this->node($id);
		if (empty($node))return array();
		return $this->node($node["parent_id"]);
	}

/**
 * Get the path to the given node
 *
 * @param int $id The ID of the record to read
 * @param string $split path to be split by delimiter {$split}
 * @return array Array of nodes from top most parent to current node
 * @access public
 */
	function getPath($id = 0, $split = "") {
		
		$node = $this->node($id);
		if (empty($node))return false;
		$pNode = load('db', 'storage')->db($this->db_name)->find($this->table_name, array( 'lft < ' => $node["lft"], 'rght > ' => $node["rght"] ), '', '*', 'lft asc' );
		$pNode = $pNode ? $pNode : array();
		array_push($pNode, $node);
		if ($split != ''){
			$tmp = "";
			foreach ($pNode as $item){
				$tmp .= $split.$item["name"];
			}
			$pNode = $tmp;
		}
		return $pNode;
		
	}

/**
 * Move the node $id to the parent node $parent_id.
 *
 *
 * @param int $id The ID of the record to move
 * @param int $parent_id
 * @return boolean true on success, false on failure
 * @access public
 */
	function moveTree($id = 0, $parent_id = 0){
		$Node = $this->node($id);
		if (empty($Node))return false;
		$pNode = $this->node($parent_id);
		load('db', 'storage')->db($this->db_name)->begin();
		//save self parent_id
		$r1 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('parent_id' => $parent_id), array('id' => $id));
		$mark1 = md5(mt_rand(100000, 999999) . time());

		if($parent_id < 1){ // move to top
			//
			if($Node['parent_id']<1){// self counld not be top node
				return false;
			}
			$lastNode = load('db', 'storage')->db($this->db_name)->find($this->table_name, array(), '0,1', '*', 'rght desc', 'one' );
			if (!empty($lastNode))$rght = $lastNode["rght"];
			else return false;

			// move self tree
			$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
				array('lft' => '`lft` + ' . ($rght - $Node["rght"]), 'rght' => '`rght` + ' . ($rght - $Node["rght"]), 'mark' => $mark1),
				array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"])
			);

			// move self rght between lastnode
			$down_number = $Node["rght"] - $Node["lft"] + 1;
			$r3 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
				array('rght' => '`rght` - ' . $down_number),
				array('rght > ' => $Node["rght"], 'rght <= ' => $rght, 'mark != ' => "'" . $mark1 . "'")
			);

			$r4 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
				array('lft' => '`lft` - ' . $down_number),
				array('lft > ' => $Node["rght"], 'lft < ' => $rght, 'mark != ' => "'" . $mark1 . "'")
			);

		}else {

			//change self::lft,self::rght where lft > self::lft and rght < self::rght

			if ($Node["lft"] > $pNode["lft"]) {// up

				$up_number = $Node["lft"] - $pNode["lft"] - 1;

				//self lft rght
				$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('lft' => '`lft` - ' . $up_number, 'rght' => '`rght` - ' . $up_number, 'mark' => $mark1),
					array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"])
				);

				//between rght and parent rght
				$down_number = $Node["rght"] - $Node["lft"] + 1;
				$r3 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('rght' => '`rght` + ' . $down_number),
					array('rght >= ' => $pNode["lft"], 'rght < ' => $Node["lft"], 'mark != ' => "'" . $mark1 . "'")
				);

				//between lft
				$r4 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('lft' => '`lft` + ' . $down_number),
					array('lft > ' => $pNode["lft"], 'lft < ' => $Node["lft"], 'mark != ' => "'" . $mark1 . "'")
				);

			} else {// down
				$down_number = $pNode["lft"] - $Node["lft"] - 1;


				//between lft and parent lft
				$up_number = $Node["rght"] - $Node["lft"] + 1;
				$r3 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('lft' => '`lft` - ' . $up_number, 'mark' => $mark1),
					array('lft > ' => $Node["rght"], 'lft <= ' => $pNode["lft"])
				);

				//between rght
				$r4 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('rght' => '`rght` - ' . $up_number, 'mark' => $mark1),
					array('rght > ' => $Node["rght"], 'rght < ' => $pNode["lft"])
				);

				$lft = $pNode["lft"] - $up_number + 1; // next node from parent after move up
				$down_number = $lft - $Node["lft"];
				//self lft rght
				$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
					array('lft' => '`lft` + ' . $down_number, 'rght' => '`rght` + ' . $down_number),
					array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"], 'mark != ' => "'" . $mark1 . "'")
				);
			}
		}
		if ($r1 && $r2 && $r3 && $r4 ){
			load('db', 'storage')->db($this->db_name)->commit();
			return true;
		}else{
			load('db', 'storage')->db($this->db_name)->rollback();
			return false;
		}
		
		
	}
	
/**
 * Reorder the node without changing the parent on the same level.
 *
 * If the node is the last child, or is a top level node with no subsequent node this method will return false
 *
 * @param int $id The ID of the record to move
 * @param int $stage how many places to move the node or true to move to last position
 * @return boolean true on success, false on failure
 * @access public
 */
	function moveDown($id = 0, $stage = 1) {
		
		$Node = $this->node($id);
		if (empty($Node) && $stage > 0)return false;
		$childrens = $this->children($Node["parent_id"], true);
		$stage_max = count($childrens);
		
		for ($i = 0; $i < $stage_max; $i++){
			if ($childrens[$i]["lft"] == $Node["lft"])break;
		}
		
		$stage = ($stage + $i) >= $stage_max ? 0 : $stage;
		if ($stage == 0 )return false;
		
		$down_number = $childrens[$i + $stage]["rght"] - $childrens[$i + 1]["lft"] + 1;
		
		$up_number = $Node["rght"] - $Node["lft"] + 1;
		
		$mark = md5(mt_rand(100000, 999999).time());
		load('db', 'storage')->db($this->db_name)->begin();
		$r1 = load('db', 'storage')->db($this->db_name)->save($this->table_name, 
				array('lft' => '`lft` + ' . $down_number, 'rght' => '`rght` + ' . $down_number, 'mark' => $mark), 
				array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"])
				);
		
		$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('lft' => '`lft` - ' . $up_number, 'rght' => '`rght` - ' . $up_number), 
				array( 'lft >= ' => $childrens[$i + 1]["lft"], 'rght <= ' => $childrens[$i + $stage]["rght"], 'mark != ' => "'" . $mark . "'")
				);
				
		if ($r1 && $r2 ){
			load('db', 'storage')->db($this->db_name)->commit();
			return true;
		}else{
			load('db', 'storage')->db($this->db_name)->rollback();
			return false;
		}
	}

/**
 * Reorder the node without changing the parent on the same level.
 *
 * If the node is the first child, or is a top level node with no previous node this method will return false
 *
 * @param int $id The ID of the record to move
 * @param int $stage how many places to move the node, or true to move to first position
 * @return boolean true on success, false on failure
 * @access public
 */
	function moveUp($id = 0, $stage = 1) {
		
		$Node = $this->node($id);
		if (empty($Node) && $stage > 0)return false;
		$childrens = $this->children($Node["parent_id"], true);
		$stage_max = count($childrens);
		
		for ($i = 0; $i < $stage_max; $i++){
			if ($childrens[$i]["lft"] == $Node["lft"])break;
		}
		
		$stage = $stage > $i ? $i : $stage;
		if ($stage == 0 )return false;
		
		$up_number = $childrens[$i-1]["rght"] - $childrens[$i - $stage]["lft"] + 1;
		
		$down_number = $Node["rght"] - $Node["lft"] + 1;
		
		$mark = md5(mt_rand(100000, 999999).time());
		load('db', 'storage')->db($this->db_name)->begin();
		$r1 = load('db', 'storage')->db($this->db_name)->save($this->table_name,
				array('lft' => '`lft` - ' . $up_number, 'rght' => '`rght` - ' . $up_number, 'mark' => $mark), 
				array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"])
				);
		
		$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('lft' => '`lft` + ' . $down_number, 'rght' => '`rght` + ' . $down_number), 
				array( 'lft >= ' => $childrens[$i - $stage]["lft"], 'rght <= ' => $childrens[$i-1]["rght"], 'mark != ' => "'" . $mark . "'")
				);
				
		if ($r1 && $r2 ){
			load('db', 'storage')->db($this->db_name)->commit();
			return true;
		}else{
			load('db', 'storage')->db($this->db_name)->rollback();
			return false;
		}
	}



/**
 * Remove the current node from the tree, and reparent all children up one level.
 *
 * If the parameter delete is false, the node will become a new top level node. Otherwise the node will be deleted
 * after the children are reparented.
 *
 * @param int $id The ID of the record to remove
 * @return boolean true on success, false on failure
 * @access public
 */
	function removeNode($id = 0) {
		$Node = $this->node($id);
		if (empty($Node))return false;
		
		load('db', 'storage')->db($this->db_name)->begin();

		$r3 = load('db', 'storage')->db($this->db_name)->delete($this->table_name, array('lft >= ' => $Node["lft"], 'rght <= ' => $Node["rght"]));
		$r1 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('lft' => '`lft` - 2'), array('lft > ' => $Node["rght"]));
		$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('rght' => '`rght` - 2'), array('rght > ' => $Node["rght"]));

		
		if ($r1 && $r2 && $r3){
			load('db', 'storage')->db($this->db_name)->commit();
			return true;
		}else{
			load('db', 'storage')->db($this->db_name)->rollback();
			return false;
		}
	}
	
/**
 * 
 * append a new node to parent node 
 * 
 * @param int $id is parent node id
 * @param array $data is parent node
 * @return boolean true on success, false on failure
 * @access public
 */
	function appendNode($id = 0, $data = array()){
		unset($data["lft"]);unset($data["rght"]);
		//root node
		if ($id == 0){
			$lastNode = load('db', 'storage')->db($this->db_name)->find($this->table_name, array(), '0,1', '*', 'rght desc', 'one' );
			if (!empty($lastNode))$lft = $lastNode["rght"];
			else $lft = 0;
			$data["parent_id"] = 0;
			$data["lft"] = $lft + 1;
			$data["rght"] = $lft + 2;
			return load('db', 'storage')->db($this->db_name)->save($this->table_name, $data);
		}else{
			$parentNode = $this->node($id);
			if (empty($parentNode))return false;
			
			//append last Children Node to the parent node
			$lastChildNode = load('db', 'storage')->db($this->db_name)->find($this->table_name, array( 'parent_id' => $id ), '0,1', '*', 'lft desc', 'one' );
	
			if (!empty($lastChildNode)){
				$lft = $lastChildNode["rght"];
				$rght = $parentNode["rght"];
			}else{
				$lft = $parentNode["lft"];
				$rght = $parentNode["rght"];
			}
			$data["parent_id"] = $id;
			$data["lft"] = $lft + 1;
			$data["rght"] = $rght + 1;

			load('db', 'storage')->db($this->db_name)->begin();
			$r1 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('lft' => '`lft` + 2'), array('lft > ' => $lft));
			$r2 = load('db', 'storage')->db($this->db_name)->save($this->table_name, array('rght' => '`rght` + 2'), array('rght >= ' => $rght));
			$r3 = load('db', 'storage')->db($this->db_name)->save($this->table_name, $data);

			
			if ($r1 && $r2 && $r3){
				load('db', 'storage')->db($this->db_name)->commit();
				return $r3;
			}else{
				load('db', 'storage')->db($this->db_name)->rollback();
				return false;
			}
		}
	}
	
	function modifyNode($id = 0, $data = array()){
		$id = intval($id);
		if($id < 1){return false;}
		unset($data['lft'], $data['rght'], $data['parent_id']);
		$data['id'] = $id;
		return load('db', 'storage')->db($this->db_name)->save($this->table_name, $data);
	}

}
