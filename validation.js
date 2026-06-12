function showError(inputId, errorId, okId) {
        document.getElementById(inputId).classList.add('invalid');
        document.getElementById(inputId).classList.remove('valid');
        document.getElementById(errorId).style.display = 'block';
        if (okId) document.getElementById(okId).style.display = 'none';
    }

    function showSuccess(inputId, errorId, okId) {
        document.getElementById(inputId).classList.add('valid');
        document.getElementById(inputId).classList.remove('invalid');
        document.getElementById(errorId).style.display = 'none';
        if (okId) document.getElementById(okId).style.display = 'block';
    }

    function validateName() {
        const name = document.getElementById('fullname').value.trim();
        if (name.length < 3) {
            showError('fullname', 'nameError', 'nameOk');
            return false;
        }
        showSuccess('fullname', 'nameError', 'nameOk');
        return true;
    }

    function validateEmail() {
        const email = document.getElementById('email').value.trim();
        // Regular expression to check valid email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('email', 'emailError', 'emailOk');
            return false;
        }
        showSuccess('email', 'emailError', 'emailOk');
        return true;
    }

    function checkPasswordStrength() {
        const pass = document.getElementById('password').value;
        const fill = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');

        let strength = 0;
        if (pass.length >= 8) strength++;
        if (/[A-Z]/.test(pass)) strength++;  // Has uppercase
        if (/[0-9]/.test(pass)) strength++;  // Has number
        if (/[^A-Za-z0-9]/.test(pass)) strength++; // Has special char

        const levels = [
            { width: '0%', color: '#e0e0e0', text: '' },
            { width: '25%', color: '#e74c3c', text: '🔴 Weak' },
            { width: '50%', color: '#e67e22', text: '🟠 Fair' },
            { width: '75%', color: '#f1c40f', text: '🟡 Good' },
            { width: '100%', color: '#27ae60', text: '🟢 Strong!' }
        ];

        fill.style.width = levels[strength].width;
        fill.style.background = levels[strength].color;
        label.textContent = levels[strength].text;
        label.style.color = levels[strength].color;

        if (pass.length < 8) {
            document.getElementById('passwordError').style.display = 'block';
            return false;
        }
        document.getElementById('passwordError').style.display = 'none';
        return strength >= 2;
    }

    function validateConfirmPass() {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPass').value;
        if (pass !== confirm || confirm === '') {
            showError('confirmPass', 'confirmError', 'confirmOk');
            return false;
        }
        showSuccess('confirmPass', 'confirmError', 'confirmOk');
        return true;
    }

    function submitForm() {
        const nameOk = validateName();
        const emailOk = validateEmail();
        const passOk = checkPasswordStrength();
        const confirmOk = validateConfirmPass();

        const success = document.getElementById('formSuccess');
        const error = document.getElementById('formError');

        if (nameOk && emailOk && passOk && confirmOk) {
            success.style.display = 'block';
            error.style.display = 'none';
        } else {
            error.style.display = 'block';
            success.style.display = 'none';
        }
    }

    // ============================================================
    // TASK 2: LIVE TEXT PREVIEW (DOM Manipulation)
    // ============================================================

    function updatePreview() {
        const text = document.getElementById('liveInput').value;
        const preview = document.getElementById('livePreview');
        preview.textContent = text === '' ? 'Your product name will appear here...' : text;
    }

    function toUpper() {
        const input = document.getElementById('liveInput');
        input.value = input.value.toUpperCase();
        updatePreview();
    }

    function toLower() {
        const input = document.getElementById('liveInput');
        input.value = input.value.toLowerCase();
        updatePreview();
    }

    function clearInput() {
        document.getElementById('liveInput').value = '';
        updatePreview();
    }

    // ============================================================
    // TASK 3: DOM SHOW/HIDE & EVENTS
    // ============================================================

    let clickCount = 0;
    let lightBg = true;

    function toggleBox(color) {
        const boxes = { blue: 'blueBox', red: 'redBox', green: 'greenBox' };
        const box = document.getElementById(boxes[color]);
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
        clickCount++;
        document.getElementById('clickCounter').textContent = 'Buttons clicked: ' + clickCount;
    }

    function hideAll() {
        ['blueBox', 'redBox', 'greenBox'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    function changeBodyColor() {
        document.body.style.background = lightBg ? '#2c3e50' : '#f0f4f8';
        lightBg = !lightBg;
        clickCount++;
        document.getElementById('clickCounter').textContent = 'Buttons clicked: ' + clickCount;
    }

    function showAlert() {
        alert(' JavaScript alert() works!\n\nThis is a basic popup triggered by an onclick event.');
        clickCount++;
        document.getElementById('clickCounter').textContent = 'Buttons clicked: ' + clickCount;
    }