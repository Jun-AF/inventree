document.getElementById('showPassword').addEventListener('click', () => {
    if (document.getElementById('Password').type == 'password') {
        document.getElementById('Password').type = 'text'
    } else if (document.getElementById('Password').type == 'text') {
        document.getElementById('Password').type = 'password'
    }
});

document.getElementById('retypePassword').addEventListener('click', () => {
    if (document.getElementById('Retype').type == 'password') {
        document.getElementById('Retype').type = 'text'
    } else if (document.getElementById('Retype').type == 'text') {
        document.getElementById('Retype').type = 'password'
    }
});

document.getElementById('Retype').addEventListener('change', (event) => {
    let password = document.getElementById('Password').value
    let retype = document.getElementById('Retype').value

    if (retype != password) {
        document.getElementById('Password').style.backgroundColor = 'rgb(255, 245, 249)'
        document.getElementsByClassName('check')[0].addEventListener('click', (event) => {
            event.preventDefault()
            window.alert('password not matches')
            return void(0)
        })
    } else {
        if (retype.length < 6) {
            document.getElementById('Password').style.backgroundColor = 'rgb(255, 245, 249)'
            document.getElementsByClassName('check')[0].addEventListener('click', (event) => {
                event.preventDefault()
                window.alert('password length not valid')
                return void(0)
            })
        } else {
            document.getElementById('Password').style.backgroundColor = 'rgb(255, 255, 255)'
            document.getElementsByClassName('check')[0].addEventListener('click', (event) => {
                document.getElementById('PasswordForm').submit()
            })
        }
    }
});
