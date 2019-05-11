<?php namespace std\ui\tree\controllers;

class Main extends InstanceController
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => $this->treeView()
                   ]);

        $this->css(':\js\jquery\ui icons');

        $this->widget(':|', $this->getWidgetData());

        return $v;
    }

    private function getWidgetData()
    {
        $widgetData = [
            '.r'     => [
                'toggleSubnodes' => $this->_p('>xhr:toggleSubnodes|'),
            ],
            'zIndex' => 100000
        ];

        if ($this->movable) {
            $droppableAccept[] = $this->_selector('|') . ' .nodes';

            ra($widgetData, [
                'droppable' => true,
                '.r/move'   => $this->_p('>xhr:move|')
            ]);
        }

        if ($this->sortable) {
            ra($widgetData, [
                'sortable'   => true,
                '.r/arrange' => $this->_p('>xhr:arrange|')
            ]);
        }

        $draggables = [];

        if ($this->droppable) {
            ra($widgetData, [
                'droppable' => true,
                '.r/drop'   => $this->_p('>xhr:drop|')
            ]);

            foreach ($this->droppable as $draggableName => $droppable) {
                if (!empty($droppable['accept'])) {
                    $droppableAccept[] = $droppable['accept'];

                    $draggables[$draggableName] = unmap($droppable, 'accept');
                }
            }
        }

        if (!empty($droppableAccept)) {
            ra($widgetData, [
                'droppableData/accept'     => implode(', ', $droppableAccept),
                'droppableData/draggables' => $draggables
            ]);
        }

        return $widgetData;
    }

    /**
     * @var \ewma\Data\Tree
     */
    private $tree;

    private function treeView()
    {
        if ($this->queryBuilder) {
            $tree = new \ewma\Data\Tree($this->_call($this->queryBuilder)->perform());
        } else {
            $tree = new \ewma\Data\Tree(new $this->model);
        }

        $tree->filterIds($this->filterIds, $this->rootNodeId);

        $this->tree = $tree->parentIdField($this->parentField);

        return $this->treeViewRecursion($this->rootNodeId);
    }

    private $level = 0;

    private function treeViewRecursion($id)
    {
        $v = $this->v('@node');

        $node = $this->tree->getNode($id);
        $subnodes = $this->tree->getSubnodes($id);

        $isRootNode = $id == $this->rootNodeId;

        $v->assign([
                       'NODE_ID'   => $id,
                       'PARENT_ID' => $node ? $node->{$this->parentField} : ''
                   ]);

        if ($node && (!$isRootNode || $this->rootNodeVisible)) {
            $nodeControlCall = $this->_call(\ewma\Data\Data::tokenize($this->nodeControl, [
                '%model' => $node
            ]));

            $inExpandList = in_array($id, $this->toggledSubnodes);
            $subnodesHidden = $isRootNode ? false : ($this->expand ? $inExpandList : !$inExpandList);

            $v->assign('node', [
                'CLASS'                  => $this->selectedNodeId == $id ? 'selected' : '',
                'INDENT_WIDTH'           => $this->level * 16 + 10 * !$isRootNode,
                'INDENT_CLICKABLE_CLASS' => $subnodes ? ' clickable' : '',
                'EXPAND_ICON_CLASS'      => !$isRootNode ? ($subnodes ? ($subnodesHidden ? 'e_arrow' : 'se_arrow') : '') : 'hidden',
                'MARGIN_LEFT'            => ($this->level - 1) * 16 + 10 * !$isRootNode,
                'CONTROL_MARGIN_LEFT'    => $this->level * 16 + 10 * !$isRootNode,
                'CONTROL'                => $nodeControlCall->perform()
            ]);
        } else {
            $subnodesHidden = false;
        }

        if ($subnodes && !$subnodesHidden) {
            $v->assign('subnodes', [
                'HIDDEN_CLASS' => $subnodesHidden ? 'hidden' : '',
            ]);

            $this->level++;

            foreach ($subnodes as $subnode) {
                $v->assign('subnodes/subnode', [
                    'CONTENT' => $this->treeViewRecursion($subnode->id)
                ]);
            }

            $this->level--;
        }

        return $v;
    }
}
