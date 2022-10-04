
<script>
    /**
     * Gets the current selected program
     */
    function getSelectedProgramId() {
        return $("#program_id").children("option:selected").val()
    }

    /**
     * Gets the price id field value if exists
     */
    function getAvailabilityId() {
        return $("#availability_id").val()
    }

    /**
     * Gets all overlapping days from other pricing but the selected day
     */
    function getRestrictedDays(callback) {
        let programId = getSelectedProgramId() || 0
        let availabilityId = getAvailabilityId()
        let url = `https://partners.{{ env('APP_DOMAIN') }}/availability/restriction/${programId}`

        // Check if it is editing form and the price id is available
        if (availabilityId) {
            url += `/${availabilityId}`
        }

        // Gets all overlapping dates from the server
        $.ajax({
            type: "GET",
            url,
            success: function (result) {
                callback(null, result)
            },
            error: function (error) {
                callback(error)
            }
        })
    }

    /**
     * Updates the datepicker
     */
    function updateDatePicker(elements, options) {
        getRestrictedDays((error, results) => {
            if (error) {
                throw "Could't Fetch Data Restrictions"
            }
            let restrictions = []

            if (!(results instanceof Array)) {
                restrictions = JSON.parse(results)
            }
            let customOptions = {
                changeMonth: true,
                changeYear: true,
                dateFormat: "dd-mm-yy",
                nextText: "Later",
                /**
                 * Disables all days that overlapping with other pricing and all days before today
                 * @param date
                 * @returns {boolean[]}
                 */
                beforeShowDay: function (date) {

                    let currentDate = new Date(jQuery.datepicker.formatDate('M D d, yy', date))
                    let now = new Date(Date.now());
                    now.setDate(now.getDate() - 1);

                    // Check if it is not a past day
                    if (now >= currentDate) {
                        return [false]
                    }

                    if (restrictions.length > 0) {
                        for (let restriction of restrictions) {
                            let startDate = new Date(restriction[0])
                            let endDate = new Date(restriction[1])

                            // Check if the day has been used before in another price
                            if (currentDate >= startDate && currentDate <= endDate) {
                                return [false]
                            }

                        }
                        return [true]
                    }

                    return [true]
                }
            };
            options = Object.assign({}, customOptions, options)
            if (elements instanceof Array) {
                for (let element of elements) {
                    $(element).datepicker("destroy")
                    $(element).datepicker(options)
                }
            } else {
                $(elements).datepicker("destroy")
                $(elements).datepicker(options)
            }
        })
    }

    // $("#program_id").select2({
    //     theme:"bootstrap"
    // });

    // updateDatePicker([$("#from-datepicker"),$("#to-datepicker"),$("#until_date")]);
    // $("#program_id").on('change', function () {
    //     updateDatePicker([$("#from-datepicker"),$("#to-datepicker")])
    // })
    $("#from-datepicker").on("change", function () {
        $("#to-datepicker").datepicker('option', {
            minDate: $("#from-datepicker").datepicker('getDate')
        })
    });

    if($(".datepicker").length){
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
        });
    }
    if($(".timepicker").length) {
        $('.timepicker').timepicker({
            showMeridian: false ,
            timeFormat : "H:i",
        });
    }
    // }if ($("#all_day").length) {--}}
    {{--    $("#all_day").on('change', function () {--}}
    {{--        if ($(this).prop("checked")) {--}}
    {{--            $(".timepicker-av").val(null);--}}
    {{--            $(".timepicker-av").attr('disabled', 'disabled');--}}
    {{--            if($("#repeat_by").val() === '{{ App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::HOURLY }}') {--}}
    {{--                $("#repeat_by option[value='{{ App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::NOREPEAT }}']").attr('selected' , 'selected');--}}
    {{--                $("#repetition_days").hide();--}}
    {{--            }--}}
    {{--            $("#repeat_by option[value='{{App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::HOURLY }}']").hide();--}}

    {{--        } else {--}}
    {{--            $(".timepicker-av").val(null);--}}
    {{--            $(".timepicker-av").attr('disabled', false);--}}
    {{--            $("#repeat_by option[value='{{App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum::HOURLY }}']").show();--}}

    {{--        }--}}
    {{--    });--}}
    {{--    $("#all_day").trigger("change");--}}
    {{--}
    {{----}}
    // joinChanel()
    $("#classroom_class_form").submit((e) => {
        e.preventDefault();
        joinChanel();
        const form = $('#classroom_class_form');
        $("#loader-wrapper").show()
        $(".validation-errors-containers").hide();

        $.ajax({
            type: "post",
            url: form.attr("action"),
            data: form.serialize(),
            success: (data) => {
            },
            error: (err) => {
                $("#loader-wrapper").hide()
                $(".validation-errors-containers").show();
                $(".validation-errors").html("");
                $.each(err.responseJSON.errors, (key, value) => {
                    $(".validation-errors").append(`<li> ${value} </li>`)
                })
            }
        })
    })

    function joinChanel() {

        const token = document.getElementById("csrfTokenSocket").value;

        Echo.connector.options.auth.headers['Authorization'] = 'Bearer ' + token

        Echo.join('createClassroomClass.' + $("#channelId").val())
            .listen('.ClassroomClassCreationEvent', (response) => {
                if (!response.status) {
                    $("#loader-wrapper").hide()
                    $(".validation-errors-containers").show();
                    $(".validation-errors").html("");
                    $.each(response.errors, (key, value) => {
                        $(".validation-errors").append(`<li> ${value} </li>`)
                    })
                } else {
                    window.location.href = "{{ isset($classroom) ? route('school-branch-supervisor.classrooms.classroomClasses.index', $classroom) : "" }}"
                }
            })
    }

</script>
