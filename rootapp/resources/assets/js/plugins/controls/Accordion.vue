<template>
        <div class="panel-group wrap" id="bs-collapse">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#" href="#one">
                                Collapse item #1
                            </a>
                        </h4>
                    </div>
                    <div id="one" class="panel-collapse collapse">
                        <div class="panel-body">
                            Where now are the horse and the rider? Where is the horn that was blowing? Where is the helm and the hauberk, and the bright hair flowing?
                        </div>
                    </div>

                </div>
                <!-- end of panel -->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#" href="#two">
                                Collapse item #2
                            </a>
                        </h4>
                    </div>
                    <div id="two" class="panel-collapse collapse">
                        <div class="panel-body">
                            Where is the harp on the harpstring, and the red fire glowing? Where is the spring and the harvest and the tall corn growing?
                        </div>

                    </div>
                </div>
                <!-- end of panel -->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#" href="#three">
                                Collapse item #3
                            </a>
                        </h4>
                    </div>
                    <div id="three" class="panel-collapse collapse">
                        <div class="panel-body">
                            The days have gone down in the West behind the hills into shadow. Who shall gather the smoke of the deadwood burning, Or behold the flowing years from the Sea returning?
                        </div>
                    </div>
                </div>
                <!-- end of panel -->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#" href="#four">
                                Collapse item #4
                            </a>
                        </h4>
                    </div>
                    <div id="four" class="panel-collapse collapse in">
                        <div class="panel-body">
                            They have passed like rain on the mountain, like a wind in the meadow; The days have gone down in the West behind the hills into shadow.
                        </div>
                    </div>
                </div>
                <!-- end of panel -->

            </div>
        <!-- end of #bs-collapse  -->
</template>

<script>
    export default {
        name: 'customAccordion',
        props: ['data'],
        data() {
            return {
                items: [
                    {
                        heading: "",
                        reports: []
                    }
                ]
            }
        }
    }
</script>