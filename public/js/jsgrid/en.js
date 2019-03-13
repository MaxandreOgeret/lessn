(function(jsGrid) {

    jsGrid.locales.en = {
        grid: {
            noDataContent: "Not found",
            deleteConfirm: "Are you sure?",
            pagerFormat: "Pages: {first} {prev} {pages} {next} {last} &nbsp;&nbsp; {pageIndex} of {pageCount}",
            pagePrevText: "Prev",
            pageNextText: "Next",
            pageFirstText: "First",
            pageLastText: "Last",
            loadMessage: "Please, wait...",
            invalidMessage: "Invalid data entered!"
        },

        loadIndicator: {
            message: "Loading..."
        },

        fields: {
            control: {
                searchModeButtonTooltip: "Switch to searching",
                insertModeButtonTooltip: "Switch to inserting",
                editButtonTooltip: "Edit",
                deleteButtonTooltip: "Delete",
                searchButtonTooltip: "Search",
                clearFilterButtonTooltip: "Clear filter",
                insertButtonTooltip: "Insert",
                updateButtonTooltip: "Update",
                cancelEditButtonTooltip: "Cancel edit"
            }
        },

        validators: {
            required: { message: "Field is required" },
            rangeLength: { message: "Field value length is out of the defined range" },
            minLength: { message: "Field value is too long" },
            maxLength: { message: "Field value is too short" },
            pattern: { message: "Field value is not matching the defined pattern" },
            range: { message: "Field value is out of the defined range" },
            min: { message: "Field value is too large" },
            max: { message: "Field value is too small" }
        }
    };

}(jsGrid, jQuery));