// API client and local auth helpers used by frontend pages
(function(){
  const STORAGE_KEY = 'gurizada:user';

  async function request(path, body) {
    const res = await fetch(path, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });
    return res.json();
  }

  function saveLocalUser(u) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(u)); } catch (e) { console.error(e); }
  }
  function getLocalUser() {
    try { const raw = localStorage.getItem(STORAGE_KEY); return raw ? JSON.parse(raw) : null; } catch { return null; }
  }
  function deleteLocalUser() { localStorage.removeItem(STORAGE_KEY); }

  function normalizeServerUser(u) {
    if (!u) return null;
    return {
      id: u.id || null,
      nome_usuario: u.nome_usuario || u.username || null,
      email: u.email || null,
      displayName: u.nome_exibicao || u.displayName || u.nome_usuario || '',
      avatar: u.avatar || 'images/profile-placeholder.png'
    };
  }

  async function fazerRegistro(nome_usuario, email, senha, nome_exibicao) {
    const resultado = await request('../api/register.php', { nome_usuario, email, senha, nome_exibicao });
    if (resultado && resultado.sucesso && resultado.usuario) {
      const normalized = normalizeServerUser(resultado.usuario);
      saveLocalUser(normalized);
      return { sucesso: true, usuario: normalized };
    }
    return resultado;
  }

  async function fazerLogin(email, senha) {
    const resultado = await request('../api/login.php', { email, senha });
    if (resultado && resultado.sucesso && resultado.usuario) {
      const normalized = normalizeServerUser(resultado.usuario);
      saveLocalUser(normalized);
      return { sucesso: true, usuario: normalized };
    }
    return resultado;
  }

  function estaAutenticado() {
    return !!getLocalUser();
  }

  async function obterUsuarioAtual() {
    // return local copy; in the future this could call an endpoint to refresh
    return getLocalUser();
  }

  async function fazerLogout() {
    deleteLocalUser();
    // optional: notify server (not implemented)
  }

  async function atualizarPerfil(dados) {
    // dados: { nome_exibicao?, avatar? (dataURL) }
    const local = getLocalUser();
    if (!local) throw new Error('Não autenticado');

    const payload = { id: local.id };
    if (dados.nome_exibicao) payload.nome_exibicao = dados.nome_exibicao;
    if (dados.avatar) payload.avatar = dados.avatar;

    const res = await request('../api/update_profile.php', payload);
    if (res && res.sucesso && res.usuario) {
      const normalized = normalizeServerUser(res.usuario);
      saveLocalUser(normalized);
      return normalized;
    }
    throw new Error(res && res.mensagem ? res.mensagem : 'Erro ao atualizar perfil');
  }

  async function deleteAccount() {
    const local = getLocalUser();
    if (!local) throw new Error('Não autenticado');
    const res = await request('../api/delete_account.php', { id: local.id });
    if (res && res.sucesso) {
      deleteLocalUser();
      return { sucesso: true };
    }
    return res;
  }

  // expose functions globally used by the HTML pages
  window.fazerRegistro = fazerRegistro;
  window.fazerLogin = fazerLogin;
  window.estaAutenticado = estaAutenticado;
  window.obterUsuarioAtual = obterUsuarioAtual;
  window.fazerLogout = fazerLogout;
  window.atualizarPerfil = atualizarPerfil;
  window.deleteAccount = deleteAccount;
})();
