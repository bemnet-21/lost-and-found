import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');
    
    const result = await login(email, password);
    if (result.success) {
      navigate('/');
    } else {
      setError(result.message || 'Login failed');
    }
    setIsLoading(false);
  };

  return (
    <div className="max-w-md mx-auto mt-12">
      <div className="bg-white p-8 rounded-[var(--radius-lg)] shadow-[var(--shadow-level-2)] border border-[var(--color-surface-dim)]">
        <div className="text-center mb-8">
          <div className="w-12 h-12 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-2xl mx-auto mb-4">
            CR
          </div>
          <h2 className="text-2xl font-bold text-[#1a1b1e]">Welcome Back</h2>
          <p className="text-[var(--color-secondary)] mt-2">Log in to CampusRetriever</p>
        </div>

        {error && (
          <div className="mb-6 p-3 bg-[var(--color-error)]/10 text-[var(--color-error)] text-sm rounded-[var(--radius-md)] border border-[var(--color-error)]/20">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Email</label>
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all"
              placeholder="you@campus.edu"
            />
          </div>

          <div>
            <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Password</label>
            <input
              type="password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            disabled={isLoading}
            className="w-full bg-[var(--color-primary)] text-white font-bold py-2.5 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors disabled:opacity-70"
          >
            {isLoading ? 'Logging in...' : 'Log In'}
          </button>
        </form>

        <div className="mt-6 text-center text-sm text-[var(--color-secondary)]">
          Don't have an account?{' '}
          <Link to="/register" className="text-[var(--color-primary)] font-bold hover:underline">
            Register here
          </Link>
        </div>
      </div>
    </div>
  );
}

// const [loginAttempts, setLoginAttempts] = useState(0);
