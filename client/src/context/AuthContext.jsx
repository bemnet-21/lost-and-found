import React, { createContext, useContext, useState, useEffect } from 'react';
import { apiFetch } from '../lib/api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const checkAuth = async () => {
    try {
      const { status, data } = await apiFetch('/check-auth.php', { method: 'GET' });
      if (status === 200 && data.success) {
        setUser(data.data);
      } else {
        setUser(null);
      }
    } catch (err) {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    checkAuth();
  }, []);

  const login = async (email, password) => {
    const { status, data } = await apiFetch('/login.php', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });
    if (status === 200 && data.success) {
      setUser(data.data);
      return { success: true };
    }
    return { success: false, message: data.message };
  };

  const register = async (username, email, password) => {
    const { status, data } = await apiFetch('/register.php', {
      method: 'POST',
      body: JSON.stringify({ username, email, password }),
    });
    if (status === 201 && data.success) {
      setUser(data.data);
      return { success: true };
    }
    return { success: false, message: data.message };
  };

  const logout = async () => {
    await apiFetch('/logout.php', { method: 'POST' });
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout, checkAuth }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
