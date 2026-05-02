import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { apiFetch } from '../lib/api';
import { MapPin, Clock, Tag, User, ArrowLeft } from 'lucide-react';

export default function ItemDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [item, setItem] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchItem = async () => {
      setLoading(true);
      try {
        const { data } = await apiFetch(`/items/single.php?id=${id}`, { method: 'GET' });
        if (data.success) {
          setItem(data.data);
        } else {
          setError(data.message || 'Item not found');
        }
      } catch (err) {
        setError('Failed to load item details.');
      } finally {
        setLoading(false);
      }
    };

    if (id) {
      fetchItem();
    }
  }, [id]);

  if (loading) {
    return (
      <div className="flex justify-center py-24">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-[var(--color-primary)]"></div>
      </div>
    );
  }

  if (error || !item) {
    return (
      <div className="max-w-2xl mx-auto text-center py-24 bg-white rounded-[var(--radius-lg)] border border-[var(--color-surface-dim)] shadow-[var(--shadow-level-1)]">
        <h2 className="text-2xl font-bold text-[#1a1b1e] mb-4">Oops!</h2>
        <p className="text-[var(--color-secondary)] mb-6">{error || 'Item not found'}</p>
        <button 
          onClick={() => navigate('/')}
          className="bg-[var(--color-primary)] text-white px-6 py-2 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors font-semibold"
        >
          Go Back Home
        </button>
      </div>
    );
  }

  const isFound = item.type === 'found';
  const isResolved = item.status === 'resolved';

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <button 
        onClick={() => navigate(-1)}
        className="flex items-center gap-2 text-[var(--color-secondary)] hover:text-[#1a1b1e] transition-colors font-medium"
      >
        <ArrowLeft size={20} />
        Back
      </button>

      <div className="bg-white rounded-[var(--radius-lg)] shadow-[var(--shadow-level-2)] border border-[var(--color-surface-dim)] overflow-hidden">
        <div className="grid grid-cols-1 md:grid-cols-2">
          
          {/* Image Side */}
          <div className="bg-[var(--color-surface-container)] min-h-[300px] flex items-center justify-center relative">
            {item.image_path ? (
              <img 
                src={`http://localhost/lost-and-found/server/${item.image_path}`} 
                alt={item.title} 
                className="w-full h-full object-cover"
              />
            ) : (
              <div className="text-[var(--color-secondary)] text-lg font-medium">
                No Image Provided
              </div>
            )}
            
            {/* Status Badge */}
            <div className="absolute top-4 right-4 flex flex-col gap-2">
              <span className={`px-4 py-1.5 rounded-[var(--radius-full)] text-sm font-bold uppercase tracking-wider shadow-md ${
                isFound 
                  ? 'bg-[var(--color-found-container)] text-[var(--color-on-found-container)]' 
                  : 'bg-[var(--color-lost-container)] text-[var(--color-on-lost-container)]'
              }`}>
                {item.type}
              </span>
              {isResolved && (
                <span className="px-4 py-1.5 rounded-[var(--radius-full)] text-sm font-bold uppercase tracking-wider bg-gray-200 text-gray-700 shadow-md">
                  Resolved
                </span>
              )}
            </div>
          </div>

          {/* Details Side */}
          <div className="p-8 flex flex-col">
            <h1 className="text-3xl font-bold text-[#1a1b1e] mb-4">
              {item.title}
            </h1>
            
            <div className="space-y-4 mb-8 flex-1">
              <div className="flex items-center gap-3 text-[var(--color-secondary)]">
                <Tag size={20} className="text-[var(--color-primary)]" />
                <span className="text-lg">{item.category}</span>
              </div>
              <div className="flex items-center gap-3 text-[var(--color-secondary)]">
                <MapPin size={20} className="text-[var(--color-primary)]" />
                <span className="text-lg">{item.location}</span>
              </div>
              <div className="flex items-center gap-3 text-[var(--color-secondary)]">
                <Clock size={20} className="text-[var(--color-primary)]" />
                <span className="text-lg">{new Date(item.created_at).toLocaleString()}</span>
              </div>
              <div className="flex items-center gap-3 text-[var(--color-secondary)]">
                <User size={20} className="text-[var(--color-primary)]" />
                <span className="text-lg">Reported by <span className="font-semibold text-[#1a1b1e]">{item.posted_by}</span></span>
              </div>
            </div>

            <div className="border-t border-[var(--color-surface-dim)] pt-6">
              <h3 className="text-lg font-bold text-[#1a1b1e] mb-2">Description</h3>
              <p className="text-[var(--color-secondary)] whitespace-pre-wrap leading-relaxed">
                {item.description}
              </p>
            </div>

            {/* Contact / Message Button (For future implementation) */}
            <div className="mt-8">
              <button 
                onClick={() => alert('Messaging will be implemented soon!')}
                className="w-full bg-[var(--color-primary)] text-white font-bold py-3 rounded-[var(--radius-md)] hover:bg-[var(--color-primary-container)] transition-colors shadow-sm"
              >
                Contact {item.posted_by}
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}

// const [viewCount, setViewCount] = useState(0);
