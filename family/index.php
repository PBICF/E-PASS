<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT FAMILY RECORDS - STANDALONE</title>
    <!-- Local CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/sweetalert.min.css">
    <link rel="stylesheet" href="../assets/css/jquery-ui.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 2rem; }
        .btn-gradient { background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%); color: white; border: none; }
        .btn-gradient:hover { opacity: 0.9; color: white; }
    </style>
</head>
<body>

<div class="container" x-data="FamilyApp()" x-init="init()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">EDIT FAMILY RECORDS</h2>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label small fw-bold">Employee No.</label>
                <input type="text" class="form-control form-control-sm" x-mask="999999" 
                    x-model="empno" placeholder="Emp No." @keydown.enter.prevent="inquire()">
                <input type="hidden" x-model="lastEmpno">
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">Sl</th>
                            <th>Name</th>
                            <th style="width:180px;">Relationship</th>
                            <th style="width:160px;">Date of Birth</th>
                            <th style="width:100px;" class="text-center">Allowed</th>
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="family.length > 0">
                            <template x-for="(member, index) in family" :key="member.fslno">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td><input type="text" class="form-control form-control-sm" x-model="member.name"></td>
                                    <td>
                                        <select class="form-select form-select-sm" x-model="member.frelation">
                                            <option value="">-- Select --</option>
                                            <template x-for="rel in relationships" :key="rel.relcode">
                                                <option :value="rel.relcode" x-text="rel.relname" :selected="rel.relcode == member.frelation"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm datepicker-input" 
                                            :value="member.db" 
                                            @focus="initDatePicker($el, member, 'db')">
                                    </td>
                                    <td class="text-center">
                                        <select class="form-select form-select-sm" x-model="member.fallowed">
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-gradient w-100" @click="updateFamily(member)">Update</button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="family.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">No records found. Enter Emp No and press Enter.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-secondary btn-sm" @click="clear()">Clear</button>
        <button class="btn btn-success btn-sm" x-show="loaded" data-bs-toggle="modal" data-bs-target="#addModal" @click="resetNewMember()">+ Add Member</button>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title h6">Add New Family Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Name</label>
                        <input type="text" class="form-control form-control-sm" x-model="newMember.name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Relationship</label>
                        <select class="form-select form-select-sm" x-model="newMember.frelation">
                            <option value="">-- Select --</option>
                            <template x-for="rel in relationships" :key="rel.relcode">
                                <option :value="rel.relcode" x-text="rel.relname"></option>
                            </template>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Date of Birth</label>
                        <input type="text" class="form-control form-control-sm" id="new_db" 
                            x-model="newMember.db" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Allowed?</label>
                        <select class="form-select form-select-sm" x-model="newMember.fallowed">
                            <option value="Y">Y</option>
                            <option value="N">N</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm px-4" @click="addFamily()">Save Member</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Local JS Dependencies -->
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/jquery-ui.js"></script>
<script src="../assets/js/bootstrap.bundle.js"></script>
<script src="../assets/js/sweetalert.min.js"></script>
<script defer src="../assets/js/mask.min.js"></script>
<script defer src="../assets/js/alpinejs.min.js"></script>

<script>
function FamilyApp() {
    return {
        empno: '',
        lastEmpno: '',
        family: [],
        relationships: [],
        loaded: false,
        newMember: { name: '', frelation: '', db: '', fallowed: 'Y' },

        init() {
            fetch('api.php?action=relationships')
                .then(r => r.json())
                .then(d => this.relationships = d.relationships);
            
            // Init datepicker for modal
            $(document).ready(() => {
                $('#new_db').datepicker({
                    dateFormat: 'dd/mm/yy',
                    onSelect: (date) => this.newMember.db = date
                });
            });
        },

        initDatePicker(el, member, field) {
            if ($(el).hasClass('hasDatepicker')) return;
            $(el).datepicker({
                dateFormat: 'dd/mm/yy',
                onSelect: (date) => member[field] = date
            }).focus();
        },

        inquire() {
            if (!this.empno) return;
            fetch('api.php?action=inquire', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `empno=${this.empno}`
            })
            .then(r => r.json())
            .then(d => {
                if (d.error) throw d.error;
                this.family = d.family;
                this.lastEmpno = this.empno;
                this.loaded = true;
            })
            .catch(err => {
                this.family = [];
                this.loaded = false;
                swal('Error', err, 'error');
            });
        },

        updateFamily(m) {
            const body = new URLSearchParams({
                empno: this.lastEmpno,
                fslno: m.fslno,
                name: m.name,
                frelation: m.frelation,
                db: m.db,
                fallowed: m.fallowed
            });

            fetch('api.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(r => r.json())
            .then(d => {
                if (d.error) throw d.error;
                swal('Success', d.success, 'success');
            })
            .catch(err => swal('Error', err, 'error'));
        },

        addFamily() {
            if (!this.newMember.name || !this.newMember.frelation) {
                return swal('Warning', 'Name and Relationship are required', 'warning');
            }

            const body = new URLSearchParams({
                empno: this.lastEmpno,
                ...this.newMember
            });

            fetch('api.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(r => r.json())
            .then(d => {
                if (d.error) throw d.error;
                bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                swal('Success', d.success, 'success');
                this.inquire();
            })
            .catch(err => swal('Error', err, 'error'));
        },

        resetNewMember() {
            this.newMember = { name: '', frelation: '', db: '', fallowed: 'Y' };
        },

        clear() {
            this.empno = '';
            this.lastEmpno = '';
            this.family = [];
            this.loaded = false;
        }
    }
}
</script>
</body>
</html>
