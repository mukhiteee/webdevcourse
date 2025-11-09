        const tabs = document.querySelectorAll('.tab');
        const forms = document.querySelectorAll('.form');
        const indicator = document.getElementById('indicator');
        const switchToSignup = document.getElementById('switchToSignup');
        const switchToLogin = document.getElementById('switchToLogin');

        function updateIndicator(activeTab) {
            const tabRect = activeTab.getBoundingClientRect();
            const tabsRect = activeTab.parentElement.getBoundingClientRect();
            indicator.style.width = tabRect.width + 'px';
            indicator.style.left = (tabRect.left - tabsRect.left) + 'px';
        }

        function switchTab(tabName) {
            tabs.forEach(t => {
                t.classList.remove('active');
                if (t.dataset.tab === tabName) {
                    t.classList.add('active');
                    updateIndicator(t);
                }
            });

            forms.forEach(f => {
                f.classList.remove('active');
                if (f.id === tabName + 'Form') {
                    f.classList.add('active');
                }
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                switchTab(tab.dataset.tab);
            });
        });

        switchToSignup.addEventListener('click', (e) => {
            e.preventDefault();
            switchTab('signup');
        });

        switchToLogin.addEventListener('click', (e) => {
            e.preventDefault();
            switchTab('login');
        });


        // Initialize indicator position
        updateIndicator(document.querySelector('.tab.active'));

        // Update indicator on window resize
        window.addEventListener('resize', () => {
            updateIndicator(document.querySelector('.tab.active'));
        });