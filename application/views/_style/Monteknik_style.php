<style>
    #page-monteknik #tblDetail {
        width: 100% !important;
    }

    #page-monteknik #tblDetail th,
    #page-monteknik #tblDetail td {
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Paksa lebar kolom Departemen, Mesin, Pelapor supaya benar-benar mengecil */
    #page-monteknik #tblDetail td:nth-child(3),
    #page-monteknik #tblDetail th:nth-child(3) {
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #page-monteknik #tblDetail td:nth-child(4),
    #page-monteknik #tblDetail th:nth-child(4) {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #page-monteknik #tblDetail td:nth-child(5),
    #page-monteknik #tblDetail th:nth-child(5) {
        max-width: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* =================== */

    #page-monteknik #tblDetail td.request-wrap,
    #page-monteknik #tblDetail th.request-wrap {
        white-space: normal !important;
        word-break: break-word;
        min-width: 250px;
        max-width: 400px;
        line-height: 16px;
    }

    #page-monteknik .table thead tr.group-header th,
    #page-monteknik .table thead tr.main-header th {
        background: #2A3F54 !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;

        text-align: center !important;
        vertical-align: middle !important;
    }

    #page-monteknik #tblDetail thead input {
        width: 100%;
        min-width: 70px;
        font-size: 11px;
        padding: 3px;
        font-weight: normal;
    }

    #page-monteknik .select2-container {
        width: 100% !important;
    }

    #page-monteknik .select2-mesin-row {
        line-height: 18px;
        padding: 3px 0;
    }

    #page-monteknik .select2-mesin-kode {
        font-weight: 700;
        color: #1ABB9C;
    }

    #page-monteknik .select2-mesin-nama {
        font-size: 12px;
        color: #555;
    }

    #page-monteknik #infoMesin {
        color: #1ABB9C !important;
        font-weight: 600;
    }

    /* BADGE STATUS & KONFIRMASI */
    #page-monteknik .badge-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
    }

    #page-monteknik .badge-status-belum {
        background-color: #e74c3c;
    }

    #page-monteknik .badge-status-proses {
        background-color: #f39c12;
    }

    #page-monteknik .badge-status-selesai {
        background-color: #1abb9c;
    }

    #page-monteknik .badge-status-default {
        background-color: #7f8c8d;
    }

    #page-monteknik #tblDetail td:nth-child(10),
    #page-monteknik #tblDetail td:nth-child(11) {
        color: #e74c3c;
        font-weight: 600;
    }

    #page-monteknik .badge-konfirmasi-true {
        background-color: #26b99a;
    }

    #page-monteknik .badge-konfirmasi-false {
        background-color: #e74c3c;
    }
</style>