<?php
function createTable(){
    return ?>
        <div id="showHideCols" class="btn-group dropleft hide">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <ul class="dropdown-menu">
                <li class="custom-control custom-checkbox">
                    <a class="small toggle-vis" data-value="1">
                    <input id='s' class="custom-control-input" type="checkbox"/>
                    <label class="custom-control-label">Name</label>
                    </a>
                </li>
                <li class="custom-control custom-checkbox">
                    <a class="small toggle-vis" data-value="2">
                    <input id='s' class="custom-control-input" type="checkbox"/>
                    <label class="custom-control-label">Position</label>
                    </a>
                </li>
                <li class="custom-control custom-checkbox">
                    <a class="small toggle-vis" data-value="3">
                    <input id='s' class="custom-control-input" type="checkbox"/>
                    <label class="custom-control-label">Office</label>
                    </a>
                </li>
                <li class="custom-control custom-checkbox">
                    <a class="small toggle-vis" data-value="4">
                    <input id='s' class="custom-control-input" type="checkbox"/>
                    <label class="custom-control-label">Age</label>
                    </a>
                </li>
                <li class="custom-control custom-checkbox">
                    <a class="small toggle-vis" data-value="5">
                    <input id='s' class="custom-control-input" type="checkbox"/>
                    <label class="custom-control-label">Salary</label>
                    </a>
                </li>
            </ul>
        </div>
            
        <div>
            <table id="example" class="table  table-bordered row-border hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Age</th>
                        <th>Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>Tiger Nixon</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        <td>61</td>
                        <td>$320,800</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Garrett Winters</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>63</td>
                        <td>$170,750</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Ashton Cox</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        <td>66</td>
                        <td>$86,000</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Cedric Kelly</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        <td>22</td>
                        <td>$433,060</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Airi Satou</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>33</td>
                        <td>$162,700</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Brielle Williamson</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        <td>61</td>
                        <td>$372,000</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Herrod Chandler</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                        <td>59</td>
                        <td>$137,500</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Rhona Davidson</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        <td>55</td>
                        <td>$327,900</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Colleen Hurst</td>
                        <td>Javascript Developer</td>
                        <td>San Francisco</td>
                        <td>39</td>
                        <td>$205,500</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Sonya Frost</td>
                        <td>Software Engineer</td>
                        <td>Edinburgh</td>
                        <td>23</td>
                        <td>$103,600</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jena Gaines</td>
                        <td>Office Manager</td>
                        <td>London</td>
                        <td>30</td>
                        <td>$90,560</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Age</th>
                        <th>Salary</th>
                    </tr>
                </tfoot>
            </table>
        </div>
<?php
}
?>