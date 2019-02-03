<div class="nodes" node_id="{NODE_ID}" parent_id="{PARENT_ID}">

    <!-- node -->
    <div class="node {CLASS}" hover="hover">
        <div class="indent {INDENT_CLICKABLE_CLASS}" hover="hover" style="width: {INDENT_WIDTH}px">
            <div class="icon {EXPAND_ICON_CLASS}"></div>
        </div>

        <div class="control" style="margin-left: {CONTROL_MARGIN_LEFT}px">{CONTROL}</div>
        <div class="cb"></div>
    </div>
    <!-- / -->

    <!-- subnodes -->
    <div class="subnodes {HIDDEN_CLASS}">
        <!-- subnodes/subnode -->
        {CONTENT}
        <!-- / -->
    </div>
    <!-- / -->

</div>
