// Shared user helper for localStorage (used by profile/settings/logout pages)
const STORAGE_KEY = 'gurizada:user';
function getUser() {
  const raw = localStorage.getItem(STORAGE_KEY);
  if (!raw) {
    const defaultUser = { displayName: 'Player', email: 'player@example.com', avatar: 'images/profile-placeholder.png' };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(defaultUser));
    return defaultUser;
  }
  try { return JSON.parse(raw); } catch { return null; }
}
function saveUser(u) { localStorage.setItem(STORAGE_KEY, JSON.stringify(u)); }
function deleteUser() { localStorage.removeItem(STORAGE_KEY); }
