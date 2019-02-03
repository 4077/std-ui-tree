<?php namespace std\ui\tree\controllers\main;

class Xhr extends \std\ui\tree\controllers\InstanceController
{
    public $allow = self::XHR;

    public function arrange()
    {
        if ($this->sortable && $this->dataHas('sequence array')) {
            if (!$this->permissions || $this->a($this->permissions)) {
                $parentModel = null;

                foreach ((array)$this->data['sequence'] as $n => $nodeId) {
                    if (is_numeric($n)) {
                        $node = (new $this->model)->find($nodeId);

                        if ($node) {
                            $node->update([$this->positionField => $n * 10]);

                            if (null === $parentModel) {
                                $parentModel = $node->parent;
                            }
                        }
                    }
                }

                if ($this->_calledMethodIn('rearrange')) {
                    $callbackData = $this->getCallbackData('sort');

                    $callbackData = \ewma\Data\Data::tokenize($callbackData, [
                        '%parent_model' => $parentModel
                    ]);

                    $this->performCallback('sort', $callbackData);
                }
            }
        }
    }

    public function move()
    {
        if ($this->movable && $this->dataHas('source_id numeric, target_id numeric')) {
            if (!$this->permissions || $this->a($this->permissions)) {
                $node = (new $this->model)->find($this->data['source_id']);

                if ($node) {
                    $node->update([
                                      $this->parentField   => $this->data['target_id'],
                                      $this->positionField => 0
                                  ]);

                    $this->data['sequence'] = $this->getSequence($this->data['target_id']);
                    $this->arrange();

                    $callbackData = $this->getCallbackData('move');

                    $callbackData = \ewma\Data\Data::tokenize($callbackData, [
                        '%source_model' => $node,
                        '%target_model' => (new $this->model)->find($this->data['target_id'])
                    ]);

                    $this->performCallback('move', $callbackData);

                    $this->c('~:reload|');
                }
            }
        }
    }

    public function drop()
    {
        if (isset($this->droppable[$this->data('draggable')])) {
            if (!$this->permissions || $this->a($this->permissions)) {
                $draggableData = $this->droppable[$this->data('draggable')];

                $callPath = $draggableData['path'];
                $callData = \ewma\Data\Data::tokenize($draggableData['data'], [
                    '%source_id' => $this->data('source_id'),
                    '%target_id' => $this->data('target_id'),
                ]);

                $this->_call([$callPath, $callData])->perform();
            }
        }
    }

    private function getSequence($parentId)
    {
        $nodes = (new $this->model)->where($this->parentField, $parentId)->orderBy($this->positionField)->get()->pluck('id')->all(); // todo  ???

        return $nodes;
    }

    public function toggleSubnodes() // todo clear deleted nodes from list
    {
        if ($this->dataHas('node_id numeric')) {
            toggle($this->s['toggled_subnodes'], $this->data['node_id']);

            $this->c('~:reload|');

            $this->performCallback('subnodesToggle');
        }
    }
}
