document.getElementById('showPassword').addEventListener('click', () => {
    if (document.getElementById('Password').type == 'password') {
        document.getElementById('Password').type = 'text'
    } else if (document.getElementById('Password').type == 'text') {
        document.getElementById('Password').type = 'password'
    }
});

document.getElementById('Password').addEventListener('change', () => {
    let password = document.getElementById('Password').value
    if (password.length > 6) {
        document.getElementById('Password').style.backgroundColor = 'rgb(255, 255, 255)'
    }
});

document.getElementById('Submit').addEventListener('click', (event) => {
    event.preventDefault();
    let password = document.getElementById('Password').value
    if (password.length < 6) {
        document.getElementById('Password').style.backgroundColor = 'rgb(255, 245, 249)'
        window.alert('password length not valid')
        return void(0)
    } else {
        document.getElementById('adminForm').submit()
    }
});