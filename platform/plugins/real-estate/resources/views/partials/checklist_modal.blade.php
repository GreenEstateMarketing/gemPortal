

<!-- The Modal -->
<div class="modal checklist_modal" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checklists</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <div class="checkbox-list d-flex align-items-center pr-3">
                <button type="button" class="btn btn-primary btn-circle btn-document mr-3">1</button>
                <h6 class="pr-2">Documents</h6>
                 <button type="button" class="btn btn-gray btn-circle btn-agent ml-4 mr-4" style="display: none">2</button>
                <h6 class="pr-2 agent-name" style="display: none">Assign Agent</h6>

                </div>
                <!--  <div class="stepwizard-step">
                      <button type="button" class="btn btn-default btn-circle2">2</button>
                      <p>Allotment Letter</p>
                  </div>
                  <div class="stepwizard-step">
                      <button type="button" class="btn btn-default btn-circle2" disabled="disabled">3</button>
                      <p>Possession Letter</p>
                  </div>-->

            <div class="documents mt-4">
                <div class="verify-documents"></div>
                <span class="red d-none" id="checklist_dp"></span>
<!--                <div class="row">
                    <div class="col">
                        <input type="checkbox"  class="checklist" id="completion_document" name="completion_document" value="1"/><span> I have verified the completion letter</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="checkbox" class="checklist" id="allotment_document" name="allotment_document" value="1"/><span> I have verified the allotment letter</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="checkbox" class="checklist" id="possession_document"  name="possession_document" value="1" /><span> I have verified the possession letter</span>
                    </div>
                </div>-->
                <div class="modal-footer">
                    <button class="btn btn-info" type="button" id="verify_checklist" class="btn btn-info next-step">Update Checklist</button>

                </div>
                <!--<button class="button" class="btn btn-info next-step">Next</button>-->
            </div>
                <div class="agents mt-4" style="display: none">
<!--                    <div class="agent_list">
                        <label>Select Agent</label>
                        <select class="form-control" id="agent_list_admin" name="agent_list_admin">

                        </select>

                    </div>-->
                    <div class="agent_list">
<!--                        <input type="hidden" id="agent_list_admin" name="agent_list_admin" />-->
                        <label for="" class="control-label">Agent Assignment</label>
                        <p class="red d-none mt-1 mb-1" id="checklist_agent"></p>
                        <div class="dropdown form-group">
                            <button class="btn btn-success
                    dropdown-toggle" type="button"
                                    id="dropdownMenuButton"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false" style="width:100% !important;">
                                Agent List
                            </button>
                            <ul class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton" id="dropdown-menu-checklist" style="width:100% !important;">


                            </ul>
                        </div>
                    </div>
                    <div class="row pt-4 agent-detail-admin d-none">
                        <div class="col-md-2"> <!-- need to dynamic image -->
                            <img src="" class="agent-image" alt="Image">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6"><b id="agent_name_admin"></b></div>
                                <div class="col-md-6"><div class="float-right d-flex" ><span class="phone-icon"><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class="label-grey" id="agent_no_admin"> &emsp;</span></div></div>
                            </div>
                            <div class="row"><div class="col label-grey" id="agent_email_admin"></div></div>
                            <div class="row"><div class="col label-grey" id="agent_desc_admin"></div></div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="modal-footer">
                                <button class="btn btn-info" type="button" id="assign_checklist" class="btn btn-info next-step">Assign</button>

                            </div>
                        </div>
                    </div>


                </div>


            </div>

        </div>
    </div>
</div>

