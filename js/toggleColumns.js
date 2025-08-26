
// The code inside the DOMContentLoaded event listener runs after the HTML document has been completely loaded and parsed.
// toggleId: The ID of the checkbox that will control the visibility.
// columnClass: The class name of the cells in the column to be toggled.
// headerIndex: The index of the header cell to be toggled.
document.addEventListener("DOMContentLoaded", function () {

    const path = window.location.pathname;
    
    if (path.includes('customer_list.php')) {
        toggleColumn("toggleCompany", "company-column", 2);
        toggleColumn("toggleEmail", "email-column", 3);
        toggleColumn("togglePhone", "phone-column", 4);
        toggleColumn("toggleNotes", "notes-column", 5);
    }
    else if (path.includes('lead_list.php')){
        toggleColumn("toggleCompany", "company-column", 2);
        toggleColumn("toggleEmail", "email-column", 3);
        toggleColumn("togglePhone", "phone-column", 4);
        toggleColumn("toggleNotes", "notes-column", 5);
        toggleColumn("toggleStatus", "status-column", 6);

    }
    else if (path.includes('interaction_list.php')){
        toggleColumn("toggleInteractionCompany", "company-column", 4);
        toggleColumn("toggleInteractionDetails", "details-column", 6);
        toggleColumn("toggleInteractionDate", "date-column", 0);
    }
    else if (path.includes('reminder_list.php')){
        toggleColumn("toggleReminderCompany", "company-column", 4);
        toggleColumn("toggleReminderNotes", "notes-column", 6);
    }

    function toggleColumn(toggleId, columnClass, headerIndex) {
        const toggleElement = document.getElementById(toggleId);
        if (!toggleElement) {
            console.error(`❌ Checkbox with ID "${toggleId}" not found!`);
            return;
        }

        console.log(`Checkbox with ID "${toggleId}" found`);
        document.getElementById(toggleId).addEventListener("change", function () {

            // Get the current state of the checkbox (checked or unchecked)
            let isVisible = this.checked; //isVisible will be true if the checkbox is checked, indicating that the column should be visible.
            let cells = document.querySelectorAll(`.${columnClass}`);
            // This line selects all table cells that belong to the specified column class (passed as columnClass to the function).
            // document.querySelectorAll returns a NodeList of all matching elements, which can be iterated over.

            let headers = document.querySelectorAll("thead th"); //This selects all <th> elements (table headers) within the <thead> of the table.
            // This allows the script to access the header corresponding to the column being toggled.
            
            if (cells.length === 0) {
                console.error(` No cells found with class "${columnClass}"`);
            } else {
                console.log(`Found ${cells.length} cells with class "${columnClass}"`);
            }

            if (!headers[headerIndex]) {
                console.error(`Header at index ${headerIndex} not found`);
            } else {
                console.log(`Header at index ${headerIndex} found`);
            }


            // Toggling cells visibility
            // This iterates over each cell in the selected column.
            // The style.display property is set to "table-cell" if isVisible is true, making the cell visible. If isVisible is false, it is set to "none", hiding the cell.
            cells.forEach(cell => {
                cell.style.display = isVisible ? "table-cell" : "none";
            });
            
            // Toggling header visibility
            // This checks if the header at the specified headerIndex exists.
            // If it does, the header's style.display property is similarly set based on the value of isVisible, toggling its visibility.
            if (headers[headerIndex]) {
                headers[headerIndex].style.display = isVisible ? "table-cell" : "none";
            }
        });
    }


});

