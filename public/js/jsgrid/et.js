(function(jsGrid) {

    jsGrid.locales.et = {
        grid: {
            noDataContent: "Ei leitud",
            deleteConfirm: "Oled kindel?",
            pagerFormat: "Leheküljed: {first} {prev} {pages} {next} {last} &nbsp;&nbsp; {pageIndex} of {pageCount}",
            pagePrevText: "Eelmine",
            pageNextText: "Järgmine",
            pageFirstText: "Esimene",
            pageLastText: "Viimane",
            loadMessage: "Palun oota...",
            invalidMessage: "Valesti sisestatud andmed!"
        },

        loadIndicator: {
            message: "Laadimine..."
        },

        fields: {
            control: {
                searchModeButtonTooltip: "Mine olekusse 'Otsing'",
                insertModeButtonTooltip: "Mine olekusse 'Sisestamine'",
                editButtonTooltip: "Muuda",
                deleteButtonTooltip: "Kustuta",

                searchButtonTooltip: "Otsi",
                clearFilterButtonTooltip: "Eemalda filter",
                insertButtonTooltip: "Sisesta",
                updateButtonTooltip: "Uuenda",
                cancelEditButtonTooltip: "Võta muudatus tagasi"
            }
        },

        validators: {
            required: { message: "Kohustuslik väli Field is required" },
            rangeLength: { message: "Väljal olev väärtus ei vasta seatud vahemikule" },
            minLength: { message: "Väljal olev väärtus on liiga pikk" },
            maxLength: { message: "Väljal olev väärtus on liiga lühike" },
            pattern: { message: "Väljal olev väärtus ei vasta seatud mustrile" },
            range: { message: "Väljal olev väärtus ei vasta seatud vahemikule" },
            min: { message: "Väljal olev väärtus on liiga suur" },
            max: { message: "Väljal olev väärtus on liiga väike" }
        }
    };

}(jsGrid, jQuery));