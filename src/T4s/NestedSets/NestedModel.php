<?php namespace T4s\NestedSets;


use illuminate\Database\Eloquent\Model;

class NestedModel extends Model
{
	/**
	 * The name of the left Column
	 * 
	 * @var string
	 */
	protected $leftColumn = 'left';

	protected $rightColumn = 'right';

	/**
	 * The parent node
	 *
	 * @var T4s\NestedSets\NestedModel
	 */
	protected $parent = null;

	/** 
	 * a array containing siblings
	 *
	 * @var
	 */
//	protected $siblings = array();

	public function initializeAsRoot()
	{
		$this->attributes[$this->leftColumn] = 1;
		$this->attributes[$this->rightColumn] = 2;
		$this->save();
	}

	/**
	 * Inserts a new child as the first child to this model
	 *
	 * @param T4s\NestedSets\NestedModel the child node
	 * @return bool success 
	 */
	public function addFirstChild(NestedModel $childNode)
	{
		$childNode->{$childNode->getLeftColumn()} =  $this->attributes[$this->leftColumn] + 1;
		$childNode->{$childNode->getRightColumn()} = $this->attributes[$this->leftColumn] + 2;
		$childNode->parent = $this;

		$this->updateNodes($childNode->{$childNode->getLeftColumn()}, 2);
		$this->updateParentNodes(2);
		
		return $childNode->save();
	}

	/**
	 * Inserts a new child as the last child to this model
	 *
	 * @param T4s\NestedSets\NestedModel the child node
	 * @return bool success 
	 */
	public function addLastChild(NestedModel $childNode)
	{
		$childNode->{$childNode->getLeftColumn()} =  $this->attributes[$this->rightColumn];
		$childNode->{$childNode->getRightColumn()} = $this->attributes[$this->rightColumn]+1;
		
		$this->updateNodes($childNode->{$childNode->getLeftColumn()}, 2);
		$this->updateParentNodes(2);

		return $childNode->save();
	}

	/**
	 * Adds a node (older sibling) to the left of this node
	 * (just incase mom or dad was unfaithfull)
	 *
	 * @param T4s\NestedSets\NestedModel the new sibling node
	 * @return bool success
	 */
	public function addOlderSibling(NestedModel $newSiblingNode)
	{
		$newSiblingNode->{$newSiblingNode->getLeftColumn()} = $this->attributes[$this->leftColumn];
		$newSiblingNode->{$newSiblingNode->getRightColumn()} = $this->attributes[$this->leftColumn]+1;
		
		$this->updateNodes($newSiblingNode->{$newSiblingNode->getLeftColumn()}, 2);

		return $newSiblingNode->save();

	}

	/**
	 * Adds a node (younger sibling) to the right of this node
	 *
	 * @param T4s\NestedSets\NestedModel the new sibling node
	 * @return bool success
	 */
	public function addYoungerSibling(NestedModel $newSiblingNode)
	{
		$newSiblingNode->{$newSiblingNode->getLeftColumn()} = $this->attributes[$this->rightColumn]+1;
		$newSiblingNode->{$newSiblingNode->getRightColumn()} = $this->attributes[$this->rightColumn]+2;
		
		$this->updateNodes($newSiblingNode->{$newSiblingNode->getLeftColumn()}, 2);
		
		return $newSiblingNode->save();
	}


	protected function updateNodes($nodeInt,$changeValue)
	{
		$this->where($this->leftColumn,'>=',$nodeInt)->increment($this->leftColumn, $changeValue);
		$this->where($this->rightColumn,'>=',$nodeInt)->increment($this->rightColumn,$changeValue);
		
		return true;
	}

	protected function updateParentNodes($changeValue)
	{
		$this->attributes[$this->rightColumn] = $this->attributes[$this->rightColumn] +$changeValue;
		if(!is_null($this->parent))
		{
			$this->parent->updateParentNodes($changeValue);
		}
	}
/*
	protected function updateSiblingNodes($side,$changeValue)
	{
		$this->attributes[$this->leftColumn] = $this->attributes[$this->leftColumn] +$changeValue;
		if($side =='left')
		{
			
			foreach ($this->siblings as $sibling) {
				$sibling->updateSiblingNodes('left',$changeValue);
			}
		}
		else if($side =='right')
		{
			
			$this->attributes[$this->rightColumn] = $this->attributes[$this->rightColumn]+$changeValue;
		}
	}
*/
	public function getLeftColumn()
	{
		return $this->leftColumn;
	}

	public function getRightColumn()
	{
		return $this->rightColumn;
	}
}
