import React from 'react';
import { Link, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Search, PlusCircle, LogOut, User } from 'lucide-react';

export default function Layout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen flex flex-col bg-[var(--color-surface)]">
      <header className="bg-white border-b border-[var(--color-surface-dim)] shadow-[var(--shadow-level-1)] sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16 items-center">
            {/* Logo */}
            <Link to="/" className="flex items-center gap-2">
              <div className="w-8 h-8 rounded bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-xl">
                CR
              </div>
              <span className="font-bold text-xl text-[var(--color-primary-container)] hidden sm:block">
                CampusRetriever
              </span>
            </Link>

            {/* Nav Links */}
            <div className="flex items-center gap-4">
              {user ? (
                <>
                  <Link 
                    to="/create" 
                    className="flex items-center gap-2 bg-[var(--color-primary)] text-white px-4 py-2 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors text-sm font-semibold"
                  >
                    <PlusCircle size={18} />
                    <span className="hidden sm:inline">Report Item</span>
                  </Link>
                  <Link 
                    to="/my-items"
                    className="flex items-center gap-2 text-[var(--color-secondary)] hover:text-[var(--color-primary)] px-3 py-2 text-sm font-medium transition-colors"
                  >
                    <User size={18} />
                    <span className="hidden sm:inline">My Items</span>
                  </Link>
                  <button 
                    onClick={handleLogout}
                    className="flex items-center gap-2 text-[var(--color-error)] hover:bg-red-50 px-3 py-2 rounded-[var(--radius-md)] text-sm font-medium transition-colors"
                  >
                    <LogOut size={18} />
                    <span className="hidden sm:inline">Logout</span>
                  </button>
                </>
              ) : (
                <>
                  <Link 
                    to="/login"
                    className="text-[var(--color-secondary)] hover:text-[var(--color-primary)] px-3 py-2 text-sm font-medium transition-colors"
                  >
                    Log In
                  </Link>
                  <Link 
                    to="/register"
                    className="bg-[var(--color-primary)] text-white px-4 py-2 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors text-sm font-semibold"
                  >
                    Register
                  </Link>
                </>
              )}
            </div>
          </div>
        </div>
      </header>

      <main className="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Outlet />
      </main>
    </div>
  );
}
