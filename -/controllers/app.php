<?php namespace std\ui\tree\controllers;

class App extends InstanceController
{
    public function collapseAll()
    {
        $this->s['toggled_subnodes'] = [];
    }

    public function expandBranch()
    {
        $node = $this->data['node'];

        $branch = \ewma\Data\Tree::getBranch($node);

        $ids = \ewma\Data\Table\Transformer::getCells($branch, 'id');

        array_pop($ids);

        if ($this->expand) {
            diff($this->s['toggled_subnodes'], $ids);
        } else {
            merge($this->s['toggled_subnodes'], $ids);
        }
    }
}
