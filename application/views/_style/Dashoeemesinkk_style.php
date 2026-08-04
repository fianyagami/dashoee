<style>
    #page-dashoeekk .filter-box {
        margin-bottom: 15px;
    }

    #page-dashoeekk .dashboard-title {
        text-align: center;
        margin: 20px 0;
        line-height: 1.4;
    }

    #page-dashoeekk .dashboard-title-main {
        font-size: 26px;
        font-weight: bold;
        color: #2A5C8A;
        text-decoration: underline;
    }

    #page-dashoeekk .dashboard-title-sub {
        font-size: 20px;
        font-weight: bold;
        color: #16A085;
    }

    #page-dashoeekk .oee-panel {
        border: 1px solid #ddd;
        padding: 12px;
        margin-bottom: 15px;
        background: #fff;
        border-radius: 4px;
    }

    #page-dashoeekk .oee-panel-title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 10px;
        text-decoration: underline;
    }

    #page-dashoeekk .chart-box {
        width: 100%;
        height: 320px;
    }

    #page-dashoeekk .oee-score {
        text-align: center;
        font-size: 52px;
        font-weight: bold;
        color: #ff00ff;
        margin-top: 45px;
    }

    #page-dashoeekk .oee-status {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        color: purple;
    }

    #page-dashoeekk .oee-formula {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: #555;
        margin-top: 15px;
    }

    #page-dashoeekk .select2-container {
        width: 100% !important;
    }

    #page-dashoeekk .select2-kk-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    #page-dashoeekk .select2-kk-nomor {
        font-weight: bold;
        width: 35%;
    }

    #page-dashoeekk .select2-kk-produk {
        width: 65%;
        color: #555;
    }

    #page-dashoeekk .oee-progress-wrap {
        margin-top: 22px;
        padding: 0 25px;
    }

    #page-dashoeekk .oee-progress-item {
        margin-bottom: 12px;
    }

    #page-dashoeekk .oee-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #555;
    }

    #page-dashoeekk .oee-progress-wrap .progress {
        height: 16px;
        margin-bottom: 0;
        border-radius: 20px;
        background: #f1f1f1;
    }

    #page-dashoeekk .oee-progress-wrap .progress-bar {
        line-height: 16px;
        border-radius: 20px;
    }

    #page-dashoeekk .oee-panel-detail-link {
        text-align: right;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed #ddd;
    }

    #page-dashoeekk .oee-panel-detail-link a {
        font-size: 14px;
        font-weight: 600;
        color: #05a73b;
        text-decoration: none;
    }

    #page-dashoeekk .oee-panel-detail-link a:hover {
        text-decoration: underline;
    }

    #page-dashoeekk .oee-panel-detail-link a.disabled {
        color: #aaa;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* MODAL */

    /* #page-dashoeekk~.modal .modal-detail-full,
    .modal-detail-full {
        width: 95vw;
        max-width: 1600px;
        margin: 20px auto;
    }

    .modal-detail-full .modal-body {
        max-height: 75vh;
        overflow-y: auto;
    }

    .modal-detail-full table.dataTable {
        width: 100% !important;
    } */

    .table-dashoeekk {
        width: 100% !important;
        table-layout: fixed;
    }

    .table-dashoeekk th,
    .table-dashoeekk td {
        white-space: normal !important;
        word-wrap: break-word;
        vertical-align: top;
        font-size: 13px;
        padding: 5px 6px !important;
    }

    .table-dashoeekk th {
        text-align: center;
        vertical-align: middle !important;
    }

    .table-dashoeekk td.text-right {
        text-align: right;
    }

    .table-dashoeekk td.text-center {
        text-align: center;
    }


    .modal-dashoeekk .modal-dialog {
        width: 98%;
        max-width: 98%;
    }

    .modal-dashoeekk .modal-body {
        overflow-x: hidden;
    }

    .modal-dashoeekk .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-dashoeekk .modal-title {
        margin: 0;
        color: #fff;
        font-weight: bold;
    }

    .modal-dashoeekk .close {
        margin: 0;
        color: #fff;
        opacity: 1;
    }

    /* MODAL */

    /* filter row */
    #tblDetailAR thead tr.filter-row input,
    #tblDetailQR thead tr.filter-row input,
    #tblDetailPR thead tr.filter-row input {
        width: 100%;
        min-width: 50px;
        font-size: 11px;
        padding: 2px 4px;
        height: 26px;
        box-sizing: border-box;
    }

    #tblDetailAR thead tr.filter-row td,
    #tblDetailQR thead tr.filter-row td,
    #tblDetailPR thead tr.filter-row td {
        padding: 4px 4px;
        background: #f9f9f9;
    }

    /* filter row */

    /* Target OEE Unit */
    #page-dashoeekk .target-unit-box {
        margin-top: 20px;
        padding: 30px;
        text-align: center;
        background: #fff3f3;
        border: 2px solid #e74c3c;
        border-radius: 6px;
    }

    #page-dashoeekk .target-unit-label {
        font-size: 17px;
        font-weight: 700;
        color: #c0392b;
        letter-spacing: 0.5px;
    }

    #page-dashoeekk .target-unit-value {
        font-size: 28px;
        font-weight: bold;
        color: #e74c3c;
        line-height: 1.2;
    }

    #page-dashoeekk .load-duration-text {
        text-align: right;
        font-size: 11px;
        color: purple;
        margin-top: 5px;
        padding-right: 5px;
    }

    /* Target OEE Unit */
</style>