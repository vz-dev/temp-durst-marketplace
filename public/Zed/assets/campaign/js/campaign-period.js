$(document).ready(function () {
    let dateFormat = 'yy-mm-dd';
    let idCampaignPeriod = $('#campaign_period_form_idCampaignPeriod').val();

    $.ajax(
        {
            method: 'GET',
            url: '/campaign/campaign-period/periods',
            data: {
                'id-campaign-period': idCampaignPeriod
            }
        }
    ).done(
        function (data) {
            start = $('#campaign_period_form_campaignStartDate').datepicker(
                {
                    dateFormat: dateFormat,
                    minDate: 0,
                    numberOfMonths: 2,
                    showAnim: 'slideDown',
                    beforeShowDay: function (date) {
                        let currentDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        return [$.inArray(currentDate, data) == -1];
                    }
                }
            ).on('change', function () {
                end.datepicker(
                    'option',
                    'minDate',
                    getDate(this)
                );
            });

            end = $('#campaign_period_form_campaignEndDate').datepicker(
                {
                    dateFormat: dateFormat,
                    minDate: 1,
                    numberOfMonths: 2,
                    showAnim: 'slideDown',
                    beforeShowDay: function (date) {
                        let currentDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        return [$.inArray(currentDate, data) == -1];
                    }
                }
            ).on('change', function () {
                start.datepicker(
                    'option',
                    'maxDate',
                    getDate(this)
                );
            });
        }
    );

    function getDate(element) {
        var date;

        try {
            date = $.datepicker.parseDate(
                dateFormat,
                element.value
            );
        } catch (error) {
            date = null;
        }

        return date;
    }
});
