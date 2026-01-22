jQuery(document).ready(function () {
    if(window.location.href.includes('action=makeorder#makeorder')) {
        let formMakeOrder = document.getElementById('makeorder');
        let tableTitleMakeOrder = formMakeOrder.querySelector('table.centpercent.notopnoleftnoright.table-fiche-title');
        tableTitleMakeOrder.parentNode.removeChild(tableTitleMakeOrder);

        Swal.fire({
            title: 'Passer Commande',
            allowOutsideClick:false,
            html: formMakeOrder,
            showConfirmButton: false,
            showClass: {
                popup: 'swal-makeorder'
            },
        });
    }
});