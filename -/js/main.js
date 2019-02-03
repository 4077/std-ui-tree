// head {
var __nodeId__ = "std_ui_tree__main";
var __nodeNs__ = "std_ui_tree";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var clickBlock = false;
            var droppableBlock = false;
            var draggableContainerId = __nodeId__ + "__draggable";

            // arrange

            if (o.sortable) {
                if (!$("#" + draggableContainerId).length) {
                    $('<div id="' + draggableContainerId + '"></div>').css('font-family', $w.css('font-family')).appendTo("body");
                }

                $(".subnodes", $w).sortable({
                    distance:             5,
                    helper:               'clone',
                    appendTo:             "#" + draggableContainerId,
                    zIndex:               o.zIndex,
                    tolerance:            'intersect',
                    placeholder:          'sortable_placeholder',
                    forcePlaceholderSize: true,
                    refreshPositions:     true,

                    start: function () {
                        clickBlock = true;
                    },

                    stop: function () {
                        setTimeout(function () {
                            clickBlock = false;
                        }, 0);
                    },

                    update: function () {
                        if (!droppableBlock) {
                            var i = 0;
                            var sequence = [];

                            $(this).find("[node_id][parent_id='" + ($(this).parent().attr("node_id") || 0) + "']")
                                .each(function () {
                                    sequence[i] = $(this).attr("node_id");
                                    i++;
                                });

                            w.r('arrange', {
                                sequence: sequence
                            });
                        }
                    }
                });
            }

            // move/drop

            if (o.droppable) {
                var droppableData = o.droppableData;

                $(".node", $w).droppable({
                    accept:      droppableData.accept,
                    activeClass: 'droppable_active',
                    hoverClass:  'droppable_hover',
                    tolerance:   'pointer',

                    drop: function (e, ui) {
                        droppableBlock = true;

                        var draggable = ui.draggable;
                        var draggableName = draggable.attr("draggable");

                        if (draggableName) {
                            var draggableData = droppableData.draggables[draggableName];

                            if (draggableData) {
                                w.r('drop', {
                                    draggable: draggableName,
                                    source_id: draggable.attr(draggableData.source_id_attr),
                                    target_id: $(this).parent().attr("node_id")
                                });
                            }
                        } else {
                            w.r('move', {
                                source_id: $(ui.helper).attr("node_id"),
                                target_id: $(this).parent().attr("node_id")
                            });
                        }

                        setTimeout(function () {
                            droppableBlock = false;
                        }, 0);
                    }
                });
            }

            // toggle subnodes

            $(".indent.clickable", $w).rebind("click", function (e) {
                w.r('toggleSubnodes', {
                    node_id: $(this).closest(".nodes").attr("node_id")
                });

                e.stopPropagation();
            });
        }
    });
})(__nodeNs__, __nodeId__);
