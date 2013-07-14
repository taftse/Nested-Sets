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
		
		$childNode->updateNodes($childNode->{$childNode->getLeftColumn()}, 2);

		$this->attributes[$this->rightColumn] = $this->attributes[$this->rightColumn] +2;

		return $childNode->save();
	}

	/*public function appendFirstChild(NestedModel $childNode)
	{
		$childNode->{$childNode->getLeftColumn()} = $this->attributes[$this->leftColumn] + 1;
		$childNode->{$childNode->getRightColumn()} = $this->attributes[$this->leftColumn] +2;

		$childNode->updateNodes($childNode->{$childNode->getLeftColumn()}, 2);

		$childNode->save();
		return $childNode;
	}

	public function appendLastChild(NestedModel $childNode)
	{
		$childNode->{$childNode->getLeftColumn()} = $this->attributes[$this->rightColumn];
		$childNode->{$childNode->getRightColumn()} = $this->attributes[$this->rightColumn]+1;

		$childNode->updateNodes($childNode->{$childNode->getLeftColumn()}, 2);

		$childNode->save();
		return $childNode;
	}

	*/

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

	public function getLeftColumn()
	{
		return $this->leftColumn;
	}

	public function getRightColumn()
	{
		return $this->rightColumn;
	}
}
