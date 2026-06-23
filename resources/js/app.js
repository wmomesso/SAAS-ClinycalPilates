import './bootstrap';
import 'flowbite';
import { Modal } from 'flowbite';
import Alpine from 'alpinejs';

window.Modal = Modal;
window.Alpine = Alpine;
Alpine.start();

// ========== DARK MODE ==========
const themeToggleButton = document.getElementById('theme-toggle');
const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

// Função para atualizar os ícones
function updateThemeIcons() {
    if (document.documentElement.classList.contains('dark')) {
        themeToggleDarkIcon?.classList.add('hidden');
        themeToggleLightIcon?.classList.remove('hidden');
    } else {
        themeToggleDarkIcon?.classList.remove('hidden');
        themeToggleLightIcon?.classList.add('hidden');
    }
}

// Inicializar tema
function initTheme() {
    const isDark = localStorage.getItem('color-theme') === 'dark' ||
        (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    updateThemeIcons();
}

// Configurar evento de toggle
if (themeToggleButton) {
    themeToggleButton.addEventListener('click', function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
        updateThemeIcons();
    });
}

// ========== COLOR THEME (5 cores) ==========
function initColorTheme() {
    const savedColor = localStorage.getItem('primary-color') || 'blue';
    document.documentElement.setAttribute('data-primary-color', savedColor);

    // Atualizar avatar se existir
    updateAvatarColor(savedColor);
}

function updateAvatarColor(color) {
    const colorMap = {
        blue: '3b82f6',
        emerald: '10b981',
        purple: 'a855f7',
        rose: 'f43f5e',
        amber: 'f59e0b'
    };
    const bgColor = colorMap[color] || '3b82f6';

    // Lista de possíveis IDs de avatar/placeholder
    const avatarSelectors = [
        '#user-avatar',
        '#user-avatar-nav',
        '#user-avatar-responsive'
    ];

    avatarSelectors.forEach(selector => {
        const avatar = document.querySelector(selector);
        // Se existe e NÃO é um avatar customizado (upload), atualiza a URL do ui-avatars
        if (avatar && !avatar.hasAttribute('data-custom-avatar') && window.userName) {
            avatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(window.userName)}&background=${bgColor}&color=fff&bold=true`;
        }
    });

    // Também podemos atualizar a cor de fundo dos placeholders baseados em DIV se existirem
    const placeholderSelectors = [
        '#user-avatar-placeholder',
        '#user-avatar-nav-placeholder',
        '#user-avatar-responsive-placeholder'
    ];

    // Mapeamento de classes de cores para o background do placeholder
    const bgClassMap = {
        blue: 'bg-primary-100',
        emerald: 'bg-emerald-100',
        purple: 'bg-purple-100',
        rose: 'bg-rose-100',
        amber: 'bg-amber-100'
    };

    placeholderSelectors.forEach(selector => {
        const placeholder = document.querySelector(selector);
        if (placeholder) {
            // Remove classes bg-primary-100, bg-emerald-100, etc. (simplificado: remove bg-*-100)
            placeholder.className = placeholder.className.replace(/\bbg-\w+-100\b/g, '');
            placeholder.classList.add(bgClassMap[color] || 'bg-primary-100');
        }
    });
}

// Configurar seletores de cor
function setupColorTheme() {
    const colorThemeToggle = document.getElementById('color-theme-toggle');
    const colorThemeDropdown = document.getElementById('color-theme-dropdown');
    const colorOptions = document.querySelectorAll('.color-theme-option');

    // Toggle dropdown
    if (colorThemeToggle && colorThemeDropdown) {
        colorThemeToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            colorThemeDropdown.classList.toggle('hidden');
        });

        // Fechar ao clicar fora
        document.addEventListener('click', (event) => {
            if (colorThemeDropdown && !colorThemeDropdown.classList.contains('hidden')) {
                if (!colorThemeToggle.contains(event.target) && !colorThemeDropdown.contains(event.target)) {
                    colorThemeDropdown.classList.add('hidden');
                }
            }
        });
    }

    // Adicionar eventos aos botões de cor
    colorOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const color = option.getAttribute('data-color');

            // Aplicar cor
            document.documentElement.setAttribute('data-primary-color', color);
            localStorage.setItem('primary-color', color);

            // Atualizar avatar
            updateAvatarColor(color);

            // Feedback visual
            option.classList.add('active');
            setTimeout(() => option.classList.remove('active'), 500);

            // Fechar dropdown
            if (colorThemeDropdown) {
                colorThemeDropdown.classList.add('hidden');
            }
        });
    });
}

// ========== USER DROPDOWN ==========
function setupUserDropdown() {
    // Menu no layout SAAS
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (userDropdown && !userDropdown.classList.contains('hidden')) {
                if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            }
        });
    }

    // Menu no layout Navigation (Legacy/App)
    const navUserButton = document.getElementById('user-menu-button-nav');
    const navUserDropdown = document.getElementById('user-dropdown-nav');

    if (navUserButton && navUserDropdown) {
        navUserButton.addEventListener('click', (e) => {
            e.stopPropagation();
            navUserDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (navUserDropdown && !navUserDropdown.classList.contains('hidden')) {
                if (!navUserButton.contains(event.target) && !navUserDropdown.contains(event.target)) {
                    navUserDropdown.classList.add('hidden');
                }
            }
        });
    }
}

// ========== ALERT CLOSE BUTTONS ==========
function setupAlertButtons() {
    document.querySelectorAll('[data-dismiss-target]').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-dismiss-target'));
            if (target) {
                target.remove();
            }
        });
    });
}

// ========== SIDEBAR / MOBILE MENU TOGGLE ==========
function setupSidebar() {
    // Sidebar drawer no layout SAAS
    const sidebarToggle = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
    const sidebar = document.getElementById('logo-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Menu responsivo no layout Navigation (Legacy/App)
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const responsiveMenu = document.getElementById('responsive-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');

    if (mobileMenuButton && responsiveMenu) {
        mobileMenuButton.addEventListener('click', () => {
            responsiveMenu.classList.toggle('hidden');
            hamburgerIcon?.classList.toggle('hidden');
            closeIcon?.classList.toggle('hidden');
        });
    }
}

// ========== INITIALIZE EVERYTHING ==========
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initColorTheme();
    setupColorTheme();
    setupUserDropdown();
    setupAlertButtons();
    setupSidebar();
});

// Exportar nome do usuário para uso no JavaScript
// Removido Blade syntax daqui. O valor agora vem de window.userName definido no layout.
