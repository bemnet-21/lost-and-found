import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { apiFetch } from '../lib/api';
import { UploadCloud, Image as ImageIcon } from 'lucide-react';

export default function CreateItem() {
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const [imagePreview, setImagePreview] = useState(null);

  const [formData, setFormData] = useState({
    title: '',
    description: '',
    type: 'lost',
    category: '',
    location: '',
  });

  const [imageFile, setImageFile] = useState(null);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      if (file.size > 2 * 1024 * 1024) {
        setError('Image size should be less than 2MB');
        return;
      }
      setImageFile(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      const data = new FormData();
      Object.keys(formData).forEach(key => data.append(key, formData[key]));
      if (imageFile) {
        data.append('image', imageFile);
      }

      const response = await apiFetch('/items/create.php', {
        method: 'POST',
        body: data,
      });

      if (response.data.success) {
        navigate('/my-items');
      } else {
        setError(response.data.message || 'Failed to create item');
      }
    } catch (err) {
      setError('An error occurred while creating the item.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto">
      <div className="bg-white p-8 rounded-[var(--radius-lg)] shadow-[var(--shadow-level-1)] border border-[var(--color-surface-dim)]">
        <h1 className="text-2xl font-bold text-[#1a1b1e] mb-2">Report an Item</h1>
        <p className="text-[var(--color-secondary)] mb-8">Fill out the details below to report an item you've lost or found on campus.</p>

        {error && (
          <div className="mb-6 p-4 bg-[var(--color-error)]/10 text-[var(--color-error)] rounded-[var(--radius-md)] border border-[var(--color-error)]/20 font-medium">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="col-span-1 md:col-span-2">
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Title</label>
              <input
                type="text"
                name="title"
                required
                value={formData.title}
                onChange={handleChange}
                placeholder="e.g. Blue Hydroflask Water Bottle"
                className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Type</label>
              <select
                name="type"
                value={formData.type}
                onChange={handleChange}
                className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all bg-white"
              >
                <option value="lost">I lost this item</option>
                <option value="found">I found this item</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Category</label>
              <select
                name="category"
                required
                value={formData.category}
                onChange={handleChange}
                className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all bg-white"
              >
                <option value="">Select a category...</option>
                <option value="Electronics">Electronics</option>
                <option value="Clothing">Clothing</option>
                <option value="Keys">Keys</option>
                <option value="Wallet/ID">Wallet/ID</option>
                <option value="Books/Supplies">Books/Supplies</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div className="col-span-1 md:col-span-2">
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Location Details</label>
              <input
                type="text"
                name="location"
                required
                value={formData.location}
                onChange={handleChange}
                placeholder="Where was it lost/found? (e.g. Student Center, Room 301)"
                className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all"
              />
            </div>

            <div className="col-span-1 md:col-span-2">
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-1">Description</label>
              <textarea
                name="description"
                required
                rows="4"
                value={formData.description}
                onChange={handleChange}
                placeholder="Provide details like color, brand, unique marks..."
                className="w-full px-4 py-2 rounded-[var(--radius-md)] border border-[var(--color-surface-dim)] focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)] outline-none transition-all resize-none"
              ></textarea>
            </div>

            <div className="col-span-1 md:col-span-2">
              <label className="block text-sm font-semibold text-[#1a1b1e] mb-2">Image (Optional)</label>
              <div className="border-2 border-dashed border-[var(--color-surface-dim)] rounded-[var(--radius-lg)] p-6 text-center hover:bg-[var(--color-surface-container)] transition-colors relative cursor-pointer">
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  onChange={handleImageChange}
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
                {imagePreview ? (
                  <div className="flex flex-col items-center">
                    <img src={imagePreview} alt="Preview" className="h-40 object-contain mb-4 rounded" />
                    <p className="text-sm text-[var(--color-primary)] font-medium">Click to change image</p>
                  </div>
                ) : (
                  <div className="flex flex-col items-center">
                    <div className="w-12 h-12 bg-[var(--color-primary-container)]/10 text-[var(--color-primary)] rounded-full flex items-center justify-center mb-3">
                      <UploadCloud size={24} />
                    </div>
                    <p className="text-sm font-medium text-[#1a1b1e] mb-1">Click to upload an image</p>
                    <p className="text-xs text-[var(--color-secondary)]">PNG, JPG, WEBP up to 2MB</p>
                  </div>
                )}
              </div>
            </div>
          </div>

          <div className="pt-6 border-t border-[var(--color-surface-dim)] flex justify-end gap-4">
            <button
              type="button"
              onClick={() => navigate('/')}
              className="px-6 py-2 rounded-[var(--radius-md)] font-semibold text-[var(--color-secondary)] hover:bg-[var(--color-surface-container)] transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={isLoading}
              className="px-6 py-2 rounded-[var(--radius-md)] font-semibold bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-container)] transition-colors disabled:opacity-70 flex items-center gap-2"
            >
              {isLoading ? 'Submitting...' : 'Submit Report'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// const [draftSaved, setDraftSaved] = useState(false);
