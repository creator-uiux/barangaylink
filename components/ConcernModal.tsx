import { useState } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from './ui/dialog';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { Textarea } from './ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select';
import { useAuth } from './AuthContext';
import { toast } from 'sonner@2.0.3';

interface ConcernModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function ConcernModal({ isOpen, onClose }: ConcernModalProps) {
  const { user } = useAuth();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    concernType: '',
    concernTitle: '',
    concernDescription: '',
    concernLocation: '',
    urgencyLevel: 'medium'
  });

  const concernTypes = [
    { value: 'infrastructure', label: 'Infrastructure' },
    { value: 'public-safety', label: 'Public Safety' },
    { value: 'sanitation', label: 'Sanitation' },
    { value: 'noise-complaint', label: 'Noise Complaint' },
    { value: 'community-service', label: 'Community Service' },
    { value: 'other', label: 'Other' }
  ];

  const urgencyLevels = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
    { value: 'emergency', label: 'Emergency' }
  ];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!formData.concernType || !formData.concernTitle || !formData.concernDescription) {
      toast.error('Please fill in all required fields');
      return;
    }

    setIsSubmitting(true);

    // Simulate API request
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Store concern in localStorage
    const concern = {
      id: Date.now(),
      userId: user?.id,
      ...formData,
      status: 'submitted',
      submittedAt: new Date().toISOString()
    };

    const concerns = JSON.parse(localStorage.getItem('barangaylink_concerns') || '[]');
    concerns.push(concern);
    localStorage.setItem('barangaylink_concerns', JSON.stringify(concerns));

    toast.success('Concern submitted successfully! We will review it and take appropriate action.');
    
    // Reset form
    setFormData({
      concernType: '',
      concernTitle: '',
      concernDescription: '',
      concernLocation: '',
      urgencyLevel: 'medium'
    });
    
    setIsSubmitting(false);
    onClose();
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Submit Concern</DialogTitle>
          <DialogDescription>
            Report community issues or submit feedback to barangay officials.
          </DialogDescription>
        </DialogHeader>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="concernType">Concern Type *</Label>
            <Select 
              value={formData.concernType} 
              onValueChange={(value) => handleInputChange('concernType', value)}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select Concern Type" />
              </SelectTrigger>
              <SelectContent>
                {concernTypes.map((type) => (
                  <SelectItem key={type.value} value={type.value}>
                    {type.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label htmlFor="concernTitle">Title *</Label>
            <Input
              id="concernTitle"
              value={formData.concernTitle}
              onChange={(e) => handleInputChange('concernTitle', e.target.value)}
              placeholder="Brief title of your concern"
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="concernDescription">Description *</Label>
            <Textarea
              id="concernDescription"
              value={formData.concernDescription}
              onChange={(e) => handleInputChange('concernDescription', e.target.value)}
              placeholder="Please describe your concern in detail..."
              rows={4}
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="concernLocation">Location</Label>
            <Input
              id="concernLocation"
              value={formData.concernLocation}
              onChange={(e) => handleInputChange('concernLocation', e.target.value)}
              placeholder="Specific location or address (optional)"
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="urgencyLevel">Urgency Level *</Label>
            <Select 
              value={formData.urgencyLevel} 
              onValueChange={(value) => handleInputChange('urgencyLevel', value)}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select Urgency Level" />
              </SelectTrigger>
              <SelectContent>
                {urgencyLevels.map((level) => (
                  <SelectItem key={level.value} value={level.value}>
                    {level.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <Button 
            type="submit" 
            className="w-full" 
            disabled={isSubmitting}
          >
            {isSubmitting ? 'Submitting Concern...' : 'Submit Concern'}
          </Button>
        </form>
      </DialogContent>
    </Dialog>
  );
}