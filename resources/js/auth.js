const API_BASE = '/api/v1';

export function getToken() {
    return localStorage.getItem('jwt_token');
}

export function setToken(token) {
    localStorage.setItem('jwt_token', token);
}

export function removeToken() {
    localStorage.removeItem('jwt_token');
}

export function authHeaders() {
    const token = getToken();
    return token ? { Authorization: `Bearer ${token}` } : {};
}

export async function apiFetch(endpoint, options = {}) {
    const headers = {
        Accept: 'application/json',
        ...authHeaders(),
    };

    // Only set Content-Type for non-FormData bodies
    if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    // Allow caller to override headers (but skip if empty object meant to clear Content-Type)
    if (options.headers && Object.keys(options.headers).length > 0) {
        Object.assign(headers, options.headers);
    }

    const res = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers,
    });

    if (res.status === 401) {
        removeToken();
        window.location.href = '/login';
        throw new Error('Unauthorized');
    }

    return res;
}

export function requireAuth() {
    if (!getToken()) {
        window.location.href = '/login';
        return false;
    }
    return true;
}

export function redirectIfAuth() {
    if (getToken()) {
        window.location.href = '/dashboard';
        return true;
    }
    return false;
}

// Make available globally for inline scripts
window.Auth = { getToken, setToken, removeToken, apiFetch, requireAuth, redirectIfAuth };
