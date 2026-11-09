window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    const datatable = document.getElementById('datatable');

    var dattableByClass = document.getElementsByClassName('datatable');

    if (dattableByClass) {
        for (var i = 0; i < dattableByClass.length; i++) {
            new simpleDatatables.DataTable(dattableByClass[i]);
        }
    }
    
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple);
    }

    if (datatable) {
            new simpleDatatables.DataTable(datatable);
    }
});
