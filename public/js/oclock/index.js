$(document).ready(function () {
    let hr = document.getElementById("hour");
    let min = document.getElementById("min");
    let sec = document.getElementById("sec");

    function displayTime() {
        let date = new Date();

        // Getting hour, mins, secs from date
        let hh = date.getHours();
        let mm = date.getMinutes();
        let ss = date.getSeconds();

        let hRotation = 30 * hh + mm / 2;
        let mRotation = 6 * mm;
        let sRotation = 6 * ss;

        hr.style.transform = `rotate(${hRotation}deg)`;
        min.style.transform = `rotate(${mRotation}deg)`;
        sec.style.transform = `rotate(${sRotation}deg)`;
    }

    setInterval(displayTime, 1000);


    const displayTimestamp = document.querySelector(".display-time");
    // Time
    function showTime() {
        let time = new Date();
        displayTimestamp.innerText = time.toLocaleTimeString("en-US", { hour12: false });
        setTimeout(showTime, 1000);
    }

    showTime();

    // Date
    function updateDate() {
        let today = new Date();

        // return number
        let dayName = today.getDay(),
            dayNum = today.getDate(),
            month = today.getMonth(),
            year = today.getFullYear(),
            timeZone = today.getTimezoneOffset();

            const timezoneOffsetInHours = -timeZone / 60;
        $("#timezone").text(timezoneOffsetInHours > 0 ? '+' + timezoneOffsetInHours : timezoneOffsetInHours)
        const months = [
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
        ];
        const dayWeek = [
            "Chủ Nhật ",
            "Thứ Hai",
            "Thứ Ba",
            "Thứ Tư",
            "Thứ Năm",
            "Thứ Sáu",
            "Thứ Bảy",
        ];
        // value -> ID of the html element
        const IDCollection = ["day", "daynum", "month", "year"];
        // return value array with number as a index
        const val = [dayWeek[dayName], dayNum, months[month], year];
        for (let i = 0; i < IDCollection.length; i++) {
            document.getElementById(IDCollection[i]).firstChild.nodeValue = val[i];
        }
    }

    updateDate();
});
