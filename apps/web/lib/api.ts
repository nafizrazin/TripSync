const BASE = process.env.NEXT_PUBLIC_API_BASE_URL ?? '/api/v1';
let csrfToken: string | null = null;

export class ApiError extends Error {
  constructor(public readonly status: number, public readonly code: string, message: string, public readonly errors?: Record<string,string[]>) { super(message); }
}

async function ensureCsrf(): Promise<string> {
  if (csrfToken) return csrfToken;
  const response = await fetch(`${BASE}/auth/csrf`, { credentials: 'include', headers: { Accept: 'application/json' } });
  if (!response.ok) throw new Error('Unable to initialize secure session.');
  const body = await response.json() as { csrf_token: string };
  csrfToken = body.csrf_token;
  return csrfToken;
}

export async function apiFetch<T>(path: string, init: RequestInit = {}): Promise<T> {
  const method = (init.method ?? 'GET').toUpperCase();
  const headers = new Headers(init.headers);
  headers.set('Accept','application/json');
  if (init.body && !headers.has('Content-Type')) headers.set('Content-Type','application/json');
  if (!['GET','HEAD','OPTIONS'].includes(method)) headers.set('X-CSRF-TOKEN', await ensureCsrf());
  const response = await fetch(`${BASE}${path}`, { ...init, headers, credentials:'include', cache:'no-store' });
  const body = await response.json().catch(() => ({})) as { message?:string; code?:string; errors?:Record<string,string[]> } & T;
  if (!response.ok) throw new ApiError(response.status, body.code ?? 'REQUEST_FAILED', body.message ?? 'Request failed.', body.errors);
  return body as T;
}
