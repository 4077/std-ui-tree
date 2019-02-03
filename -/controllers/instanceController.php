<?php namespace std\ui\tree\controllers;

class InstanceController extends \Controller
{
    protected $s;

    public $queryBuilder;

    public $model;

    public $filterIds;

    public $rootNodeId;

    public $rootNodeVisible;

    public $parentField;

    public $positionField;

    public $sortable;

    public $movable;

    public $droppable;

    public $permissions;

    public $selectedNodeId;

    public $nodeControl;

    public $expand;

    public $toggledSubnodes;

    public function __create()
    {
        $this->s = &$this->s('~|');

        if (!$this->s) {
            $this->instanceSessionSetDefaults();
        }

        $this->instanceSessionUpdate(unmap($this->data, 'default, callbacks'));
        $this->initFromInstanceSession();
        $this->setCallbacks();
    }

    private function setCallbacks()
    {
        $d = &$this->d('~|');

        remap($d, $this->data, 'callbacks');
    }

    public function performCallback($name, $data = [])
    {
        $callbacks = $this->d('~:callbacks|');

        if (isset($callbacks[$name])) {
            $this->_call($callbacks[$name])->ra($data)->perform();
        }
    }

    public function getCallbackData($name)
    {
        $callbacks = $this->d('~:callbacks|');

        if (isset($callbacks[$name])) {
            return $this->_call($callbacks[$name])->data();
        }
    }

    private function instanceSessionSetDefaults()
    {
        ap($this->s, false, [
            'query_builder'     => false,
            'model'             => false,
            'filter_ids'        => false,
            'root_node_id'      => 0,
            'root_node_visible' => true,
            'parent_field'      => 'parent_id',
            'position_field'    => 'position',
            'sortable'          => false,
            'movable'           => false,
            'perform_move'      => true,
            'droppable'         => false,
            'permissions'       => false,
            'selected_node_id'  => false,
            'node_control'      => false,
            'expand'            => false,
            'toggled_subnodes'  => [],
        ]);

        $this->instanceSessionUpdate($this->data('default'));
    }

    private function instanceSessionUpdate($data)
    {
        remap($this->s, $data, '
            query_builder, model, filter_ids, root_node_id, 
            root_node_visible, parent_field, position_field, 
            sortable, movable, droppable,
            permissions, selected_node_id, expand
        ');

        if (!empty($data['query_builder'])) {
            $this->s['query_builder'] = $this->_caller()->_abs($data['query_builder']);

            $builder = $this->_call($this->s['query_builder'])->perform();

            $this->s['model'] = get_class($builder->getModel());
        }

        if (isset($data['node_control'])) {
            $this->s['node_control'] = $this->_caller()->_abs($data['node_control']);
        }

        if (isset($data['droppable'])) {
            foreach ($data['droppable'] as $droppableName => $droppable) {
                $this->s['droppable'][$droppableName]['path'] = $this->_callerP($data['droppable'][$droppableName]['path']);
            }
        }
    }

    private function initFromInstanceSession()
    {
        \ewma\Data\Data::extract($this, $this->s, '
            queryBuilder        query_builder,
            model               model,
            filterIds           filter_ids,
            rootNodeId          root_node_id,
            rootNodeVisible     root_node_visible,
            parentField         parent_field,
            positionField       position_field,
            sortable            sortable,
            movable             movable,
            droppable           droppable,
            permissions         permissions,
            selectedNodeId      selected_node_id,
            nodeControl         node_control,
            expand              expand,
            toggledSubnodes     toggled_subnodes
        ');
    }
}
