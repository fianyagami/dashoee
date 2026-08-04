<style>
    #page-komplplhp #tblPlp,
    #page-komplplhp #tblLhp {
        width: 100% !important;
    }

    #page-komplplhp #tblPlp th,
    #page-komplplhp #tblPlp td,
    #page-komplplhp #tblLhp th,
    #page-komplplhp #tblLhp td {
        white-space: nowrap;
        vertical-align: middle;
        font-size: 12px;
    }

    #page-komplplhp #tblPlp td.request-wrap,
    #page-komplplhp #tblPlp th.request-wrap,
    #page-komplplhp #tblLhp td.produk-wrap,
    #page-komplplhp #tblLhp th.produk-wrap {
        white-space: normal !important;
        word-break: break-word;
        min-width: 200px;
        max-width: 300px;
        line-height: 15px;
    }

    /* Waktu Start & Waktu Finish di panel PLP -> font merah (konsisten dengan Monteknik) */
    #page-komplplhp #tblPlp td:nth-child(6),
    #page-komplplhp #tblPlp td:nth-child(7) {
        color: #e74c3c;
        font-weight: 600;
    }

    /* Jam Mulai & Jam Selesai di panel LHP -> font merah, sama seperti tblPlp */
    #page-komplplhp #tblLhp td:nth-child(7),
    #page-komplplhp #tblLhp td:nth-child(8) {
        color: #e74c3c;
        font-weight: 600;
    }

    /* Row focus saat diklik -> background hijau muda, tanpa ubah warna font */
    #page-komplplhp #tblPlp tbody tr.row-selected td,
    #page-komplplhp #tblLhp tbody tr.row-selected td {
        background-color: #d5f5e3 !important;
    }

    #page-komplplhp #tblPlp thead input,
    #page-komplplhp #tblLhp thead input {
        width: 100%;
        min-width: 60px;
        font-size: 11px;
        padding: 3px;
        font-weight: normal;
    }

    #page-komplplhp .select2-container {
        width: 100% !important;
    }

    #page-komplplhp .select2-mesin-row {
        line-height: 18px;
        padding: 3px 0;
    }

    #page-komplplhp .select2-mesin-kode {
        font-weight: 700;
        color: #1ABB9C;
    }

    #page-komplplhp .select2-mesin-nama {
        font-size: 12px;
        color: #555;
    }
</style>