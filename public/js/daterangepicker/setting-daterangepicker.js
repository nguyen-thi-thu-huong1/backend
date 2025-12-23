function settingDaterangePicker(id) {
    $(id).daterangepicker({
        // startDate: (new Date()).setHours(0, 0, 0, 0),
        // endDate: new Date(),
        showCustomRangeLabel: true,
        alwaysShowCalendars: true,
        autoUpdateInput: false,
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        ranges: {
            'Hôm nay': [(new Date()).setHours(0, 0, 0, 0), new Date()],
            'Hôm qua': [getYesterday()['start'], getYesterday()['end']],
            'Tuần này': [getThisWeek()['start'], getThisWeek()['end']],
            'Tuần trước': [getLastWeek()['start'], getLastWeek()['end']],
            'Tháng này': [getThisMonth()['start'], getThisMonth()['end']],
            'Tháng trước': [getLastMonth()['start'], getLastMonth()['end']],
            '6 tháng trước': [getSixLastMonth()['start'], getSixLastMonth()['end']],
        },
        locale: {
            cancelLabel: 'Xóa',
            applyLabel: 'Xác nhận',
            format: 'DD/MM/YYYY HH:mm:ss',
            customRangeLabel: 'Tùy chọn',
            daysOfWeek: [
                "CN",
                "T2",
                "T3",
                "T4",
                "T5",
                "T6",
                "T7",
            ],
            monthNames: [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12",
            ]
        },
    }, cb);

    $(id).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm:ss') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm:ss'));
    });

    $(id).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}
function cb(start, end) {
    $('#created_at span').html(start + ' - ' + end);
}

cb(formatDate((new Date()).setHours(0, 0, 0, 0)), formatDate(new Date()));

function formatDate(timestamp) {
    const date = new Date(timestamp); // Tạo đối tượng Date từ timestamp
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Tháng trong JavaScript bắt đầu từ 0
    const year = date.getFullYear();

    const formattedDate = `${day}/${month}/${year}`;
    return formattedDate;
}

function getYesterday() {

    const today = new Date();

    const yesterdayStart = new Date(today);
    yesterdayStart.setDate(today.getDate() - 1);
    yesterdayStart.setHours(0, 0, 0, 0); // Đặt giờ về 00:00:00

    const yesterdayEnd = new Date(yesterdayStart);
    yesterdayEnd.setHours(23, 59, 59, 999); // Đặt giờ về 23:59:59.999

    return {
        start: yesterdayStart,
        end: yesterdayEnd
    };
}

function getThisWeek() {
    const today = new Date();

    if (isNaN(today)) {
        console.error('today không phải là đối tượng Date hợp lệ');
        return;
    }

    const currentDay = today.getDay();

    const firstDayOfWeek = new Date(today);
    const diffToMonday = currentDay === 0 ? -6 : 1 - currentDay;
    firstDayOfWeek.setDate(today.getDate() + diffToMonday);
    firstDayOfWeek.setHours(0, 0, 0, 0);

    return {
        'start': firstDayOfWeek,
        'end': today
    };
}

function getLastWeek() {
    const today = new Date();

    const currentDay = today.getDay();

    const lastMonday = new Date(today);
    const diffToLastMonday = currentDay === 0 ? -13 : -6 - currentDay;
    lastMonday.setDate(today.getDate() + diffToLastMonday);
    lastMonday.setHours(0, 0, 0, 0);

    const lastSunday = new Date(lastMonday);
    lastSunday.setDate(lastMonday.getDate() + 6);
    lastSunday.setHours(23, 59, 59, 999);

    return {
        'start': lastMonday,
        'end': lastSunday
    };
}

function getThisMonth() {
    const today = new Date();

    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    firstDayOfMonth.setHours(0, 0, 0, 0); // Đặt giờ về 00:00:00

    const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    lastDayOfMonth.setHours(23, 59, 59, 999); // Đặt giờ về 23:59:59.999

    return {
        'start': firstDayOfMonth,
        'end': today
    };
}

function getLastMonth() {
    const today = new Date();

    const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    firstDayOfLastMonth.setHours(0, 0, 0, 0);

    const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    lastDayOfLastMonth.setHours(23, 59, 59, 999);

    return {
        'start': firstDayOfLastMonth,
        'end': lastDayOfLastMonth
    };
}

function getSixLastMonth() {
    const today = new Date();

    const firstDayOfSixMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 6, 1);
    firstDayOfSixMonthsAgo.setHours(0, 0, 0, 0);

    const lastDayOfSixMonthsAgo = new Date(today.getFullYear(), today.getMonth() - 5, 0);
    lastDayOfSixMonthsAgo.setHours(23, 59, 59, 999);

    return {
        'start': firstDayOfSixMonthsAgo,
        'end': today
    };
}
