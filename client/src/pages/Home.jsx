import React, { useState, useEffect, useCallback } from 'react';
import { apiFetch } from '../lib/api';
import ItemCard from '../components/ItemCard';
import { Search } from 'lucide-react';

export default function Home() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Filters
  const [activeTab, setActiveTab] = useState('all'); // 'all', 'lost', 'found'
  const [searchQuery, setSearchQuery] = useState('');

  const fetchItems = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      let endpoint = '/items/read.php';
      
      // If we have a search query (min 2 chars), use search endpoint
      if (searchQuery.length >= 2) {
        endpoint = `/items/search.php?q=${encodeURIComponent(searchQuery)}`;
      } else if (activeTab !== 'all') {
        endpoint = `/items/read.php?type=${activeTab}`;
      }

      const { data } = await apiFetch(endpoint, { method: 'GET' });
      if (data.success) {
        setItems(data.data);
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('Failed to load items. Make sure the backend is running.');
    } finally {
      setLoading(false);
    }
  }, [activeTab, searchQuery]);

  useEffect(() => {
    // Debounce search slightly
    const timer = setTimeout(() => {
      fetchItems();
    }, 300);
    return () => clearTimeout(timer);
  }, [fetchItems]);

  return (
    <div className="space-y-8">
      {/* Header & Controls Area */}
      <div className="bg-white p-6 rounded-[var(--radius-lg)] shadow-[var(--shadow-level-1)] border border-[var(--color-surface-dim)] space-y-6">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 className="text-3xl font-bold text-[#1a1b1e]">Campus Feed</h1>
            <p className="text-[var(--color-secondary)] mt-1">Browse recently lost and found items across campus.</p>
          </div>
          
          {/* Search Bar */}
          <div className="relative w-full md:w-72">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search size={18} className="text-[var(--color-secondary)]" />
            </div>
            <input
              type="text"
              placeholder="Search items..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="block w-full pl-10 pr-3 py-2 border border-[var(--color-surface-dim)] rounded-[var(--radius-md)] bg-[var(--color-surface-container-lowest)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition-all"
            />
          </div>
        </div>

        {/* Tabs */}
        <div className="flex gap-2 border-b border-[var(--color-surface-dim)]">
          {['all', 'lost', 'found'].map((tab) => (
            <button
              key={tab}
              onClick={() => { setActiveTab(tab); setSearchQuery(''); }}
              className={`px-4 py-3 text-sm font-semibold capitalize border-b-2 transition-colors ${
                activeTab === tab && !searchQuery
                  ? 'border-[var(--color-primary)] text-[var(--color-primary)]'
                  : 'border-transparent text-[var(--color-secondary)] hover:text-[#1a1b1e] hover:border-[var(--color-surface-dim)]'
              }`}
            >
              {tab === 'all' ? 'All Items' : tab}
            </button>
          ))}
        </div>
      </div>

      {/* Content Area */}
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
        <div className="text-center py-24 bg-white rounded-[var(--radius-lg)] border border-[var(--color-surface-dim)] shadow-sm">
          <div className="text-6xl mb-4">🔍</div>
          <h3 className="text-xl font-bold text-[#1a1b1e] mb-2">No items found</h3>
          <p className="text-[var(--color-secondary)]">
            {searchQuery 
              ? "We couldn't find anything matching your search." 
              : "There are no items reported in this category right now."}
          </p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {items.map((item) => (
            <ItemCard key={item.id} item={item} />
          ))}
        </div>
      )}
    </div>
  );
}
