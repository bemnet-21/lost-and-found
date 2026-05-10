import React, { useState, useEffect, useCallback } from 'react';
import { apiFetch } from '../lib/api';
import { useAuth } from '../context/AuthContext';
import ItemCard from '../components/ItemCard';
import { useNavigate } from 'react-router-dom';

export default function MyItems() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const fetchMyItems = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const { data } = await apiFetch('/items/read.php', { method: 'GET' });
      if (data.success) {
        // Filter out items not owned by user (since our endpoint doesn't have a my-items specific query yet)
        // Note: For a production app, the backend should have an endpoint specifically for user's items.
        // But since we can't easily filter by user_id in the current API without a specific query param,
        // we'll fetch all and filter client side for this demo, or we can check if posted_by matches user.username.
        const myItems = data.data.filter(item => item.posted_by === user.username);
        setItems(myItems);
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to load items.');
    } finally {
      setLoading(false);
    }
  }, [user]);

  useEffect(() => {
    if (user) {
      fetchMyItems();
    }
  }, [user, fetchMyItems]);

  const handleStatusChange = async (itemId, status) => {
    try {
      const { data } = await apiFetch('/items/update.php', {
        method: 'PUT',
        body: JSON.stringify({ id: itemId, status }),
      });
      if (data.success) {
        setItems(items.map(i => i.id === itemId ? { ...i, status } : i));
      } else {
        alert(data.message || 'Failed to update status');
      }
    } catch (err) {
      alert('Error updating status');
    }
  };

  const handleDelete = async (itemId) => {
    if (!window.confirm('Are you sure you want to delete this item?')) return;
    try {
      const { data } = await apiFetch('/items/delete.php', {
        method: 'DELETE',
        body: JSON.stringify({ id: itemId }),
      });
      if (data.success) {
        setItems(items.filter(i => i.id !== itemId));
      } else {
        alert(data.message || 'Failed to delete item');
      }
    } catch (err) {
      alert('Error deleting item');
    }
  };

  if (!user) {
    return (
      <div className="text-center py-12">
        <h2 className="text-2xl font-bold mb-4">Please log in to view your items</h2>
        <button 
          onClick={() => navigate('/login')}
          className="bg-[var(--color-primary)] text-white px-6 py-2 rounded-[var(--radius-md)]"
        >
          Log In
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-[#1a1b1e]">My Items</h1>
          <p className="text-[var(--color-secondary)] mt-1">Manage the items you've reported</p>
        </div>
        <button 
          onClick={() => navigate('/create')}
          className="bg-[var(--color-primary)] text-white px-4 py-2 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors font-semibold"
        >
          Report New Item
        </button>
      </div>

      {error && (
        <div className="bg-[var(--color-error)]/10 text-[var(--color-error)] p-4 rounded-[var(--radius-md)]">
          {error}
        </div>
      )}

      {loading ? (
        <div className="flex justify-center py-12">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-[var(--color-primary)]"></div>
        </div>
      ) : items.length === 0 ? (
        <div className="text-center py-24 bg-white rounded-[var(--radius-lg)] border border-[var(--color-surface-dim)]">
          <h3 className="text-xl font-bold text-[#1a1b1e] mb-2">You haven't reported any items yet</h3>
          <p className="text-[var(--color-secondary)] mb-6">If you lost or found something, you can report it here.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {items.map((item) => (
            <ItemCard 
              key={item.id} 
              item={item} 
              isOwner={true} 
              onStatusChange={handleStatusChange}
              onDelete={handleDelete}
            />
          ))}
        </div>
      )}
    </div>
  );
}
