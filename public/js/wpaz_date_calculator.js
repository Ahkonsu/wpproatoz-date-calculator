jQuery(document).ready(function($) {

    $(document).on('click', '.slider', function() {      
        const slideRef = $(this);
        const inputChange = $(slideRef).closest('.switch').find('input[type="hidden"]');
        var currVal = inputChange.val();
        inputChange.val(currVal * -1);
    });  
    
    // Click event for #calculate button
    $(document).on('click', '.wpazdc_calc #calculate', function() {        
        var $calcContainer = $(this).closest('.wpazdc_calc');
        var number = parseInt($calcContainer.find('#add_unit').val());
        var workdayOption = false;
        var workhourOption = false;
        var holidayOption = false;
        var output_format = $calcContainer.find('#output_format').val();
console.log(output_format);
        console.log('line 31 backforwards ' + number);

        var workhours = $calcContainer.find('input#workhours');
        if (workhours.val() == 1) {
    		workhourOption = true;
		}

        var workdays = $calcContainer.find('input#workdays');
        if (workdays.val() == 1) {
    		workdayOption = true;
		}		

        var backforwards = $calcContainer.find('input#backforwards');
        number = number * backforwards.val();

        if (isNaN(number)) {
            console.warn("Invalid number input.");
            return;
        }
        
        var selectedUnit = $calcContainer.find('#the_unit').val();
        var output = computeOutput(selectedUnit, number, workdayOption, workhourOption, holidayOption, $calcContainer, output_format);
        $calcContainer.find('#result').html("Here's our set date: " + output);
    });

    // Change event for #the_unit dropdown
    $('.wpazdc_calc').on('change', '#the_unit', function() {
        var selectedUnit = $(this).val();
        if (selectedUnit !== 'hour') {
            $(this).closest('.wpazdc_calc').find('.time').fadeOut();
        } else {
            $(this).closest('.wpazdc_calc').find('.time').fadeIn('fast');
        }
    });
});

function computeOutput(selectedUnit, number, workdayOption, workhours, holidayHours, $calcContainer, outputFormat = "YYYY-MM-DD HH:mm:ss A") {
    var result = "nothing yet";
    var desiredDate = $calcContainer.find('#desired_date').val().replace(/-/g, '/'); // Convert to Safari-friendly format
    var desiredTime = $calcContainer.find('#desired_time').val() || "00:00:00";
    var initialDate = new Date(desiredDate + " " + desiredTime);

    if (workhours) {
        var newNumber;
        if (parseInt(number) < 0) {
            newNumber = ((Math.abs(number) % 8) + (Math.trunc(Math.abs(number) / 8) * 24)) * -1;
        } else {
            newNumber = (number % 8) + (Math.trunc(number / 8) * 24);
        }
        number = newNumber;
    }

    switch (selectedUnit) {
        case 'hour':
            result = new Date(initialDate.getTime() + (number * 3600 * 1000));
            break;
        case 'day':
            if (workdayOption) {
                for (var i = 0; i < Math.abs(number); i++) {
                    if (initialDate.getDay() === 0 || initialDate.getDay() === 6) {
                        initialDate.setDate(initialDate.getDate() + (number < 0 ? -2 : 2));
                    } else {
                        initialDate.setDate(initialDate.getDate() + (number < 0 ? -1 : 1));
                    }
                }
            } else {
                initialDate.setDate(initialDate.getDate() + number);
            }
            result = initialDate;
            break;
        case 'week':
            result = new Date(initialDate.getTime() + (number * 7 * 24 * 3600 * 1000));
            break;
        case 'month':
            initialDate.setMonth(initialDate.getMonth() + number);
            result = initialDate;
            break;
        case 'year':
            initialDate.setFullYear(initialDate.getFullYear() + number);
            result = initialDate;
            break;
    }

    // Convert result into the desired format
    return formatResult(result, outputFormat);
}


function formatResult(dateObj, format = "YYYY-MM-DD HH:mm:ss A") {
    const replacements = {
        YYYY: dateObj.getFullYear(),
        MM: (dateObj.getMonth() + 1).toString().padStart(2, '0'),
        DD: dateObj.getDate().toString().padStart(2, '0'),
        HH: dateObj.getHours().toString().padStart(2, '0'),
        mm: dateObj.getMinutes().toString().padStart(2, '0'),
        ss: dateObj.getSeconds().toString().padStart(2, '0'),
        A: dateObj.getHours() >= 12 ? 'PM' : 'AM',
        h: (dateObj.getHours() % 12 || 12).toString().padStart(2, '0') // 12-hour format
    };

    // Replace placeholders in the format string with actual values
    return format.replace(/YYYY|MM|DD|HH|mm|ss|A|h/g, match => replacements[match]);
}



function getAMorPM(dateObj) {
    var hours = dateObj.getHours();
    return hours >= 12 ? 'PM' : 'AM';
}

