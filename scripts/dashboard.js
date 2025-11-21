            const header = document.getElementById('header');
            const sections = document.getElementById('sections');
        function toggleModule(header, sections) {
            const chevron = header.querySelector('.chevron');
            const isExpanded = sections.classList.contains('show');
           
            sections.classList.toggle('show');
            chevron.classList.toggle('expanded');
            header.setAttribute('aria-expanded', !isExpanded);
        }

        function selectSection(element, sectionName) {
            document.querySelectorAll('.section-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
            console.log(`Navigating to: ${sectionName}`);
           
            // Close mobile menu on selection
            if (window.innerWidth <= 968) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }

        // // Update User Info
        // function updateUserInfo() {
        //     const name = analyticsData.userName;
        //     const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
           
        //     document.getElementById('userName').textContent = name;
        //     document.getElementById('welcomeName').textContent = name.split(' ')[0];
        //     document.getElementById('userAvatar').textContent = initials;
        // }

        function animateNumber(elementId, start, end, duration, suffix = '') {
            const element = document.getElementById(elementId);
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
           
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = Math.round(current * 10) / 10 + suffix;
            }, 16);
        }

        function animateCounter(elementId, current, total) {
            const element = document.getElementById(elementId);
            let count = 0;
            const duration = 1500;
            const increment = current / (duration / 16);
           
            const timer = setInterval(() => {
                count += increment;
                if (count >= current) {
                    count = current;
                    clearInterval(timer);
                }
                element.textContent = `${Math.round(count)}/${total}`;
            }, 16);
        }

        function updateBarChart() {
            const barChart = document.getElementById('barChart');
            barChart.innerHTML = '';
           
            analyticsData.moduleProgress.forEach((module, index) => {
                const barItem = document.createElement('div');
                barItem.className = 'bar-item';
                barItem.innerHTML = `
                    <div class="bar-label">
                        <span>${module.name}</span>
                        <span class="bar-percentage">${module.completion}%</span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: 0%"></div>
                    </div>
                `;
                barChart.appendChild(barItem);
               
                // Animate bars with staggered delay
                setTimeout(() => {
                    barItem.querySelector('.bar-fill').style.width = module.completion + '%';
                }, 200 + (index * 150));
            });
        }

        function updatePieChart(percentage) {
            const circumference = 2 * Math.PI * 102;
            const offset = circumference - (percentage / 100) * circumference;
           
            setTimeout(() => {
                document.getElementById('pieSegment').style.strokeDasharray =
                    `${circumference} ${circumference}`;
                document.getElementById('pieSegment').style.strokeDashoffset = offset;
                animateNumber('piePercentage', 0, percentage, 1500, '%');
            }, 200);
        }

        // Mobile Menu Toggle
        document.getElementById('menuToggle').addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
           
            if (window.innerWidth <= 968 &&
                sidebar.classList.contains('active') &&
                !sidebar.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Simulate data refresh (like database updates)
        function refreshData() {
            // Simulate fetching new data
            const mainContent = document.querySelector('.main-content');
            mainContent.classList.add('loading');
           
            setTimeout(() => {
                // Update with new data (simulated)
                analyticsData.completedQuizzes = Math.min(
                    analyticsData.completedQuizzes + Math.floor(Math.random() * 2),
                    analyticsData.totalQuizzes
                );
                analyticsData.uploadedProjects = Math.min(
                    analyticsData.uploadedProjects + Math.floor(Math.random() * 2),
                    analyticsData.totalProjects
                );
               
                updateAnalytics();
                mainContent.classList.remove('loading');
            }, 1000);
        }

        // Keyboard Navigation
        document.addEventListener('keydown', (e) => {
            // Press 'r' to refresh data
            if (e.key === 'r' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    refreshData();
                }
            }
        });

        // Initialize Dashboard
        document.addEventListener('DOMContentLoaded', () => {
            updateAnalytics();
           
            // Auto-refresh data every 30 seconds (simulating live updates)
            setInterval(() => {
                // Only auto-refresh if tab is visible
                if (!document.hidden) {
                    // Uncomment to enable auto-refresh
                    // refreshData();
                }
            }, 30000);
        });