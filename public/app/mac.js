document.getElementById('type').addEventListener('change', function() {
    let type = this.value;
    if (type == 'Others') {
        document.getElementsByClassName("mca")[0].style.display = "none";
    } else {
        document.getElementsByClassName("mca")[0].style.display = "flex";
    }
});