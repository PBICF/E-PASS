function handleStationCodeInput(element, selector, callback = null) {
    // Only allow letters A-Z, no spaces or other characters
    element.addEventListener('input', (e) => e.target.value = e.target.value.toUpperCase().replace(/[^A-Z]/g, ''));

    element.addEventListener('change', (e) => {
        let match = false;
        if(e.target.value == '') {
            document.querySelector(selector).innerText = '';
            e.target.classList.remove('is-invalid');
            return;
        }

        STATION.forEach((station) => {
            if (station.SCODE === e.target.value.toUpperCase()) {
                document.querySelector(selector).innerText = station.SNAME;
                e.target.classList.remove('is-invalid');
                match = true;
            }
        });

        if (!match) {
            e.target.classList.add('is-invalid');
            document.querySelector(selector).innerText = '';
        }

        if (typeof callback == 'function') {
            callback();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    let allowBack = false;
    history.pushState(null, "", location.href);

    window.addEventListener("popstate", function (event) {
        if (allowBack) {
            return;
        }

        Swal.fire({
            title: "Leave this page?",
            text: "If you proceed, all form data will be cleared.",
            icon: "warning",
            width: '350px',
            heightAuto: false,
            showCancelButton: true,
            confirmButtonText: "Yes, leave",
            cancelButtonText: "Stay",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                allowBack = true;
                history.back(); // allow actual back
            } else {
                history.pushState(null, "", location.href); // stay
            }
        });
    });

    document.querySelector('[name="from_station_code"]').addEventListener('input', (e) => {
        // Only allow letters A-Z, no spaces or other characters
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z]/g, '');
    });

    document.querySelector('[name="from_station_code"]').addEventListener('change', (e) => {
        let match = false;
        if(e.target.value == '') {
            e.target.nextElementSibling.innerText = '';
            e.target.classList.remove('is-invalid');
            return;
        }
        STATION.forEach((station) => {
            if (station.SCODE === e.target.value.toUpperCase()) {
                document.querySelector('[name="from_station_name"]').value = station.SNAME;
                document.querySelector('[name="from_station_name_hindi"]').value = station.SNAME_HINDI ?? '';
                match = true;
                e.target.classList.remove('is-invalid');
            }
        });

        if (!match) {
            e.target.classList.add('is-invalid');
        }
    });

    document.querySelector('[name="to_station_code"]').addEventListener('input', (e) => {
        // Only allow letters A-Z, no spaces or other characters
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z]/g, '');
    });

    document.querySelector('[name="to_station_code"]').addEventListener('change', (e) => {
        let match = false;
        if(e.target.value == '') {
            e.target.nextElementSibling.innerText = '';
            e.target.classList.remove('is-invalid');
            return;
        }
        STATION.forEach((station) => {
            if (station.SCODE === e.target.value.toUpperCase()) {
                document.querySelector('[name="to_station_name"]').value = station.SNAME;
                document.querySelector('[name="to_station_name_hindi"]').value = station.SNAME_HINDI ?? '';
                match = true;
                e.target.classList.remove('is-invalid');
            }
        });

        if (!match) {
            e.target.classList.add('is-invalid');
        }
    });

    document.querySelectorAll('[name="via[]"]').forEach((input) => {
        handleStationCodeInput(input, '.via_code_details', function() {
            // If "Different Return via?" is NOT checked, keep return[] inputs in sync
            if (differentReturnCheckbox && !differentReturnCheckbox.checked) {
                syncReturnFromVia();
            }
        });
    });

    document.querySelectorAll('[name="return_via[]"]').forEach((input) => {
        handleStationCodeInput(input, '.return_code_details');
    });

    document.querySelectorAll('[name="break_journey[]"]').forEach((input) => {
        handleStationCodeInput(input, '.bj_code_details', function() {
            syncReturnFromBreak();
        });
    });

    document.querySelectorAll('[name="break_journey_return[]"]').forEach((input) => {
        handleStationCodeInput(input, '.bjr_code_details');
    });

    // Disable/enable Break Journey (Return) inputs when Single/Return is changed
    const singleReturnSelect = document.querySelector('[name="single_return"]');
    function updateBreakReturnDisabled() {
        const breakReturnInputs = document.querySelectorAll('[name="break_journey_return[]"]');
        if (!singleReturnSelect) return;
        const singleSelected = singleReturnSelect.value === '1';
        breakReturnInputs.forEach(input => {
            if (singleSelected) {
                if (input.value !== '') {
                    input.value = '';
                    input.dispatchEvent(new Event('change'));
                }
                input.setAttribute('disabled', 'disabled');
                input.classList.remove('is-invalid');
            } else {
                input.removeAttribute('disabled');
            }
        });
    }

    if (singleReturnSelect) {
        singleReturnSelect.addEventListener('change', updateBreakReturnDisabled);
        // initial state
        updateBreakReturnDisabled();
    }

    // Helper: sync return inputs from break inputs when "Different Return via?" is NOT checked
    const differentReturnCheckbox = document.getElementById('different_return_via');

    function syncReturnFromBreak() {
        const breakInputs = Array.from(document.querySelectorAll('[name="break_journey[]"]'));
        const returnInputs = Array.from(document.querySelectorAll('[name="break_journey_return[]"]'));

        // Gather non-empty break values, uppercase
        const vals = breakInputs.map(i => i.value.trim().toUpperCase()).filter(v => v.length > 0);
        const rev = [...vals].reverse();

        // Write reversed values into return inputs, clear the rest
        returnInputs.forEach((input, idx) => {
            const newVal = rev[idx] ?? '';
            if (input.value !== newVal) {
                input.value = newVal;
                // trigger change so validation runs and station names are updated
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    // Helper: sync return_via[] from via[] (reverse order) when "Different Return via?" is NOT checked
    function syncReturnFromVia() {
        const viaInputs = Array.from(document.querySelectorAll('[name="via[]"]'));
        const returnInputs = Array.from(document.querySelectorAll('[name="return_via[]"]'));

        // Gather non-empty via values, uppercase
        const vals = viaInputs.map(i => i.value.trim().toUpperCase()).filter(v => v.length > 0);
        const rev = vals.slice().reverse();

        // Write reversed values into return inputs, clear the rest
        returnInputs.forEach((input, idx) => {
            const newVal = rev[idx] ?? '';
            if (input.value !== newVal) {
                input.value = newVal;
                // trigger change so validation runs and station names are updated
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    // Keep inputs enabled/disabled and synced when checkbox changes; run once on init
    if (differentReturnCheckbox) {
        differentReturnCheckbox.addEventListener('change', (e) => {
            const breakReturnInputs = document.querySelectorAll('[name="break_journey_return[]"]');
            const viaReturnInputs = document.querySelectorAll('[name="return_via[]"]');

            // If unchecked -> auto-fill reversed values and disable manual editing (Alpine already binds :disabled)
            if (!e.target.checked) {
                // Immediately sync values
                syncReturnFromBreak();
                syncReturnFromVia();

                // ensure disabled if Alpine hasn't yet applied it
                viaReturnInputs.forEach(i => i.setAttribute('disabled', 'disabled'));
            } else {
                viaReturnInputs.forEach(i => {
                    i.value = '';
                    if (i.nextElementSibling) i.nextElementSibling.innerText = '';
                    i.classList.remove('is-invalid');
                    i.removeAttribute('disabled');
                });
            }
        });

        // Initial run: if not checked, sync reversed values and set disabled
        if (!differentReturnCheckbox.checked) {
            syncReturnFromBreak();
            syncReturnFromVia();
            document.querySelectorAll('[name="return_via[]"]') .forEach(i => i.setAttribute('disabled', 'disabled'));
        }
    }

    // Trigger change on prefilled inputs so station names/validation show immediately
    ['from_station_code', 'to_station_code'].forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el && el.value) el.dispatchEvent(new Event('change'));
    });

    document.querySelectorAll('[name="via[]"]').forEach(i => { if (i.value) i.dispatchEvent(new Event('change')) });
    document.querySelectorAll('[name="return_via[]"]').forEach(i => { if (i.value) i.dispatchEvent(new Event('change')) });
    document.querySelectorAll('[name="break_journey[]"]').forEach(i => { if (i.value) i.dispatchEvent(new Event('change')) });
    document.querySelectorAll('[name="break_journey_return[]"]').forEach(i => { if (i.value) i.dispatchEvent(new Event('change')) });
});

function formatDate(date) {
    const dd = String(date.getDate()).padStart(2, '0');
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const yyyy = date.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
}