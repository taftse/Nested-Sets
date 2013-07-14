<?php namespace T4s\NestedSets;


use illuminate\Database\Eloquent\Model;

class NestedModel extends Model
{

	protected $leftColumn = 'left';

	protected $rightColumn = 'right';

	public function initializeRoot()
	{
		$this->attributes[$this->leftColumn] = 1;
		$this->attributes[$this->rightColumn] = 2;
		$this->save();
	}

	public function appendFirstChild(NestedModel $childNode)
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

	

	protected function updateNodes($nodeInt,$changeValue)
	{
		NestedModel::where($this->leftColumn,'>=',$nodeInt)->increment($this->leftColumn, $changeValue);
		NestedModel::where($this->rightColumn,'>=',$nodeInt)->increment($this->rightColumn,$changeValue);

		return true;
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
