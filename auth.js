/* registration(sign up) logic */
const registrationForm = document.getElementById('signin-form');

if (registrationForm) {
    registrationForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(registrationForm);
        
        const data = Object.fromEntries(formData.entries());

        data.photo_url = ""; 

        console.log("Отправляем данные:", data); 

        try {
            const response = await fetch('http://localhost:8888/wuide/api.php/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data) 
            });

            const result = await response.json();
            console.log("Ответ сервера:", result);

            if (response.ok) {
                alert('Success! Registration complete. Please Log In.');
                
                registrationForm.reset();
                
                document.getElementById('reg_login_id').click(); 
                
            } else {
                alert('Error: ' + (result.error || result.message));
            }

        } catch (error) {
            console.error('Ошибка сети:', error);
            alert('Server error. Check console for details.');
        }
    });
}


/* log in logic*/

const loginForm = document.getElementById('login-form');

if (loginForm) {
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());

        console.log("Попытка входа:", data.email);

        try {
            const response = await fetch('http://localhost:8888/wuide/api.php/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log("Ответ сервера:", result);

            if (response.ok) {
                alert('Login successful! Welcome back.');

                localStorage.setItem('authToken', result.token);
                localStorage.setItem('userId', result.userId);

                closeModal(); 
            } else {
                alert('Login failed: ' + (result.error || result.message));
            }

        } catch (error) {
            console.error('Ошибка сети:', error);
            alert('Server error. Check console.');
        }
    });
}


/* authorization checking */

async function checkAuth() {
    const token = localStorage.getItem('authToken');

    if (!token) return;

    console.log("Токен найден, проверяем пользователя...");

    try {
        const response = await fetch('http://localhost:8888/wuide/api.php/auth/profile', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token 
            }
        });

        if (response.ok) {
            const result = await response.json();
            const user = result.user;

            console.log("Пользователь:", user);

            const navContainer = document.getElementById('main_reg_id');
            
            if (navContainer) {
                navContainer.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span style="font-family: 'Nunito'; font-weight: 700; font-size: 18px; color: #333;">
                            Hello, ${user.name}
                        </span>
                        <button id="logout-btn" class="reg_signin" style="width: auto; padding: 10px 20px; background: #000; color: #fff; cursor: pointer;">
                            Log out
                        </button>
                    </div>
                `;

                document.getElementById('logout-btn').addEventListener('click', () => {
                    localStorage.removeItem('authToken');
                    localStorage.removeItem('userId');
                    
                    window.location.reload();
                });
            }

        } else {
            console.log("Токен невалиден");
            localStorage.removeItem('authToken');
        }

    } catch (error) {
        console.error('Ошибка проверки авторизации:', error);
    }
}

checkAuth();