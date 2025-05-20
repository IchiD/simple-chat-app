/**
 * セキュリティユーティリティ関数
 * XSS対策とローカルストレージのトークン保護を強化
 */

/**
 * トークンを簡易暗号化する
 * 注意: これは完全な暗号化ではなく難読化です。本番環境ではより強力な方法を検討してください。
 */
export function encryptToken(token: string): string {
  if (!token) return "";

  // 単純な難読化: Base64エンコード + 文字列反転
  const reversed = token.split("").reverse().join("");
  const salt = generateSalt();
  const combined = `${salt}:${reversed}`;
  return btoa(combined);
}

/**
 * 暗号化されたトークンを復号化する
 */
export function decryptToken(encryptedToken: string | null): string {
  if (!encryptedToken) return "";

  try {
    // Base64デコード + 文字列反転を元に戻す
    const decoded = atob(encryptedToken);
    const [, reversed] = decoded.split(":");
    return reversed.split("").reverse().join("");
  } catch (error) {
    console.error("トークン復号化エラー:", error);
    return "";
  }
}

/**
 * 入力値をサニタイズしてXSS攻撃を防止
 */
export function sanitizeInput(input: string): string {
  if (!input) return "";

  const element = document.createElement("div");
  element.textContent = input;
  return element.innerHTML;
}

/**
 * HTML特殊文字をエスケープ
 */
export function escapeHtml(unsafe: string): string {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

/**
 * ブラウザフィンガープリントを生成（簡易版）
 * ユーザー環境の特徴を取得して認証の追加検証に使用
 */
export function generateBrowserFingerprint(): string {
  if (!import.meta.client) return "";

  const components = [
    navigator.userAgent,
    navigator.language,
    screen.colorDepth,
    screen.width + "x" + screen.height,
    new Date().getTimezoneOffset(),
    !!navigator.cookieEnabled,
  ];

  // 単純なハッシュ生成
  return btoa(components.join("###"));
}

/**
 * ソルト（塩）を生成
 */
function generateSalt(): string {
  return Math.random().toString(36).substring(2, 10);
}
