import React from 'react';
import { MapPin, Clock, Tag } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function ItemCard({ item, isOwner = false, onStatusChange, onDelete }) {
  const isFound = item.type === 'found';
  const isResolved = item.status === 'resolved';

  return (
    <Link to={`/item/${item.id}`} className={`bg-white rounded-[var(--radius-lg)] border border-[var(--color-surface-dim)] shadow-[var(--shadow-level-1)] overflow-hidden flex flex-col transition-all hover:shadow-[var(--shadow-level-2)] ${isResolved ? 'opacity-70' : ''}`}>
      {/* Image Area */}
      <div className="h-48 bg-[var(--color-surface-container)] relative">
        {item.image_path ? (
          <img 
            src={`http://localhost/lost-and-found/server/${item.image_path}`} 
            alt={item.title} 
            className="w-full h-full object-cover"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-[var(--color-secondary)] text-sm font-medium">
            No Image Provided
          </div>
        )}
        
        {/* Status Badge */}
        <div className="absolute top-3 right-3 flex flex-col gap-2">
          <span className={`px-3 py-1 rounded-[var(--radius-full)] text-xs font-bold uppercase tracking-wider shadow-sm ${
            isFound 
              ? 'bg-[var(--color-found-container)] text-[var(--color-on-found-container)]' 
              : 'bg-[var(--color-lost-container)] text-[var(--color-on-lost-container)]'
          }`}>
            {item.type}
          </span>
          {isResolved && (
            <span className="px-3 py-1 rounded-[var(--radius-full)] text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-700 shadow-sm">
              Resolved
            </span>
          )}
        </div>
      </div>

      {/* Content Area */}
      <div className="p-5 flex-1 flex flex-col">
        <h3 className="text-lg font-bold text-[#1a1b1e] mb-2 line-clamp-1" title={item.title}>
          {item.title}
        </h3>
        
        <p className="text-sm text-[var(--color-secondary)] mb-4 line-clamp-2 flex-1">
          {item.description}
        </p>

        <div className="space-y-2 mb-4">
          <div className="flex items-center gap-2 text-sm text-[var(--color-secondary)]">
            <Tag size={16} className="shrink-0 text-[var(--color-primary)]" />
            <span className="truncate">{item.category}</span>
          </div>
          <div className="flex items-center gap-2 text-sm text-[var(--color-secondary)]">
            <MapPin size={16} className="shrink-0 text-[var(--color-primary)]" />
            <span className="truncate">{item.location}</span>
          </div>
          <div className="flex items-center gap-2 text-sm text-[var(--color-secondary)]">
            <Clock size={16} className="shrink-0 text-[var(--color-primary)]" />
            <span className="truncate">{new Date(item.created_at).toLocaleDateString()}</span>
          </div>
        </div>

        {/* Footer / Actions */}
        <div className="pt-4 border-t border-[var(--color-surface-dim)] flex justify-between items-center">
          <div className="text-xs text-[var(--color-secondary)] font-medium">
            By <span className="text-[var(--color-primary-container)]">{item.posted_by}</span>
          </div>
          
          {isOwner && (
            <div className="flex gap-2">
              {!isResolved && (
                <button 
                  onClick={(e) => { e.preventDefault(); onStatusChange(item.id, 'resolved'); }}
                  className="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-[var(--radius-sm)] font-semibold transition-colors"
                >
                  Resolve
                </button>
              )}
              <button 
                onClick={(e) => { e.preventDefault(); onDelete(item.id); }}
                className="text-xs bg-red-50 hover:bg-red-100 text-[var(--color-error)] px-3 py-1.5 rounded-[var(--radius-sm)] font-semibold transition-colors"
              >
                Delete
              </button>
            </div>
          )}
        </div>
      </div>
    </Link>
  );
}
