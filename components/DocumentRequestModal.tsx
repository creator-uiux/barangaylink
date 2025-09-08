import { useState } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from './ui/dialog';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { Textarea } from './ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './ui/select';
import { useAuth } from './AuthContext';
import { toast } from 'sonner@2.0.3';

interface DocumentRequestModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function DocumentRequestModal({ isOpen, onClose }: DocumentRequestModalProps) {
  const { user } = useAuth();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    documentType: '',
    purpose: '',
    contactNumber: '',
    additionalNotes: ''
  });

  const documentTypes = [
    { value: 'barangay-clearance', label: 'Barangay Clearance' },
    { value: 'certificate-residency', label: 'Certificate of Residency' },
    { value: 'certificate-indigency', label: 'Certificate of Indigency' },
    { value: 'business-permit', label: 'Business Permit' },
    { value: 'id-replacement', label: 'ID Replacement' }
  ];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!formData.documentType || !formData.purpose || !formData.contactNumber) {
      toast.error('Please fill in all required fields');
      return;
    }

    setIsSubmitting(true);

    // Simulate API request
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Store request in localStorage
    const request = {
      id: Date.now(),
      userId: user?.id,
      ...formData,
      status: 'pending',
      submittedAt: new Date().toISOString()
    };

    const requests = JSON.parse(localStorage.getItem('barangaylink_requests') || '[]');
    requests.push(request);
    localStorage.setItem('barangaylink_requests', JSON.stringify(requests));

    toast.success('Document request submitted successfully! You will be notified when it\'s ready.');
    
    // Reset form
    setFormData({
      documentType: '',
      purpose: '',
      contactNumber: '',
      additionalNotes: ''
    });
    
    setIsSubmitting(false);
    onClose();
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle>Request Document</DialogTitle>
          <DialogDescription>
            Submit a request for barangay certificates and official documents.
          </DialogDescription>
        </DialogHeader>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="documentType">Document Type *</Label>
            <Select 
              value={formData.documentType} 
              onValueChange={(value) => handleInputChange('documentType', value)}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select Document Type" />
              </SelectTrigger>
              <SelectContent>
                {documentTypes.map((type) => (
                  <SelectItem key={type.value} value={type.value}>
                    {type.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <Label htmlFor="purpose">Purpose *</Label>
            <Input
              id="purpose"
              value={formData.purpose}
              onChange={(e) => handleInputChange('purpose', e.target.value)}
              placeholder="e.g., Employment, Business, etc."
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="contactNumber">Contact Number *</Label>
            <Input
              id="contactNumber"
              type="tel"
              value={formData.contactNumber}
              onChange={(e) => handleInputChange('contactNumber', e.target.value)}
              placeholder="Your contact number"
              required
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="additionalNotes">Additional Notes</Label>
            <Textarea
              id="additionalNotes"
              value={formData.additionalNotes}
              onChange={(e) => handleInputChange('additionalNotes', e.target.value)}
              placeholder="Any additional information..."
              rows={3}
            />
          </div>

          <Button 
            type="submit" 
            className="w-full" 
            disabled={isSubmitting}
          >
            {isSubmitting ? 'Submitting Request...' : 'Submit Request'}
          </Button>
        </form>
      </DialogContent>
    </Dialog>
  );
}