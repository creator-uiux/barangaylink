import { useState, useEffect } from 'react';
import { Button } from './ui/button';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Badge } from './ui/badge';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from './ui/tabs';
import { useAuth } from './AuthContext';
import { 
  Link, 
  Users, 
  FileText, 
  MessageSquare, 
  Settings, 
  LogOut,
  Search,
  Filter,
  Calendar,
  Clock,
  UserCheck
} from 'lucide-react';
import { toast } from 'sonner@2.0.3';

interface AdminDashboardProps {
  onNavigateToLanding: () => void;
}

interface DocumentRequest {
  id: number;
  userId: number;
  documentType: string;
  purpose: string;
  contactNumber: string;
  additionalNotes: string;
  status: 'pending' | 'approved' | 'completed' | 'rejected';
  submittedAt: string;
}

interface Concern {
  id: number;
  userId: number;
  concernType: string;
  concernTitle: string;
  concernDescription: string;
  concernLocation: string;
  urgencyLevel: 'low' | 'medium' | 'high' | 'emergency';
  status: 'submitted' | 'in-progress' | 'completed';
  submittedAt: string;
}

interface User {
  id: number;
  fullName: string;
  email: string;
  role: 'admin' | 'user';
  createdAt: string;
}

export function AdminDashboard({ onNavigateToLanding }: AdminDashboardProps) {
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [currentSection, setCurrentSection] = useState<'overview' | 'requests' | 'concerns' | 'users'>('overview');
  const [allRequests, setAllRequests] = useState<DocumentRequest[]>([]);
  const [allConcerns, setAllConcerns] = useState<Concern[]>([]);
  const [allUsers, setAllUsers] = useState<User[]>([]);
  const [searchTerm, setSearchTerm] = useState('');

  const handleLogout = () => {
    logout();
    toast.success('Logged out successfully');
    onNavigateToLanding();
  };

  const handleSectionChange = (section: 'overview' | 'requests' | 'concerns' | 'users') => {
    setCurrentSection(section);
    setIsMenuOpen(false);
  };

  // Load all data when component mounts
  useEffect(() => {
    loadAllRequests();
    loadAllConcerns();
    loadAllUsers();
  }, []);

  const loadAllRequests = () => {
    try {
      const requests = JSON.parse(localStorage.getItem('barangaylink_requests') || '[]');
      setAllRequests(requests.sort((a: DocumentRequest, b: DocumentRequest) => 
        new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime()
      ));
    } catch (error) {
      console.error('Error loading requests:', error);
      setAllRequests([]);
    }
  };

  const loadAllConcerns = () => {
    try {
      const concerns = JSON.parse(localStorage.getItem('barangaylink_concerns') || '[]');
      setAllConcerns(concerns.sort((a: Concern, b: Concern) => 
        new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime()
      ));
    } catch (error) {
      console.error('Error loading concerns:', error);
      setAllConcerns([]);
    }
  };

  const loadAllUsers = () => {
    try {
      const users = JSON.parse(localStorage.getItem('barangaylink_users') || '[]');
      setAllUsers(users.filter((u: User) => u.role === 'user').sort((a: User, b: User) => 
        new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
      ));
    } catch (error) {
      console.error('Error loading users:', error);
      setAllUsers([]);
    }
  };

  const updateRequestStatus = (requestId: number, newStatus: 'pending' | 'approved' | 'completed' | 'rejected') => {
    try {
      const requests = JSON.parse(localStorage.getItem('barangaylink_requests') || '[]');
      const updatedRequests = requests.map((req: DocumentRequest) => 
        req.id === requestId ? { ...req, status: newStatus } : req
      );
      localStorage.setItem('barangaylink_requests', JSON.stringify(updatedRequests));
      loadAllRequests();
      toast.success('Request status updated successfully');
    } catch (error) {
      console.error('Error updating request status:', error);
      toast.error('Failed to update request status');
    }
  };

  const updateConcernStatus = (concernId: number, newStatus: 'submitted' | 'in-progress' | 'completed') => {
    try {
      const concerns = JSON.parse(localStorage.getItem('barangaylink_concerns') || '[]');
      const updatedConcerns = concerns.map((concern: Concern) => 
        concern.id === concernId ? { ...concern, status: newStatus } : concern
      );
      localStorage.setItem('barangaylink_concerns', JSON.stringify(updatedConcerns));
      loadAllConcerns();
      toast.success('Concern status updated successfully');
    } catch (error) {
      console.error('Error updating concern status:', error);
      toast.error('Failed to update concern status');
    }
  };

  const getUserById = (userId: number) => {
    const users = JSON.parse(localStorage.getItem('barangaylink_users') || '[]');
    return users.find((u: User) => u.id === userId);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getStatusBadgeColor = (status: string) => {
    switch (status) {
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'approved': return 'bg-green-100 text-green-800';
      case 'completed': return 'bg-blue-100 text-blue-800';
      case 'rejected': return 'bg-red-100 text-red-800';
      case 'submitted': return 'bg-blue-100 text-blue-800';
      case 'in-progress': return 'bg-orange-100 text-orange-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getUrgencyBadgeColor = (urgency: string) => {
    switch (urgency) {
      case 'low': return 'bg-gray-100 text-gray-800';
      case 'medium': return 'bg-yellow-100 text-yellow-800';
      case 'high': return 'bg-orange-100 text-orange-800';
      case 'emergency': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getDocumentTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
      'barangay-clearance': 'Barangay Clearance',
      'certificate-residency': 'Certificate of Residency',
      'certificate-indigency': 'Certificate of Indigency',
      'business-permit': 'Business Permit',
      'id-replacement': 'ID Replacement'
    };
    return labels[type] || type;
  };

  const getConcernTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
      'infrastructure': 'Infrastructure',
      'public-safety': 'Public Safety',
      'sanitation': 'Sanitation',
      'noise-complaint': 'Noise Complaint',
      'community-service': 'Community Service',
      'other': 'Other'
    };
    return labels[type] || type;
  };

  const renderOverviewSection = () => (
    <section className="py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">Admin Dashboard</h1>
          <p className="text-gray-600">Monitor and manage all barangay activities</p>
        </div>

        {/* Statistics Cards */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Users</p>
                  <p className="text-2xl font-bold text-blue-600">{allUsers.length}</p>
                </div>
                <Users className="w-8 h-8 text-blue-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Document Requests</p>
                  <p className="text-2xl font-bold text-green-600">{allRequests.length}</p>
                </div>
                <FileText className="w-8 h-8 text-green-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Concerns Submitted</p>
                  <p className="text-2xl font-bold text-orange-600">{allConcerns.length}</p>
                </div>
                <MessageSquare className="w-8 h-8 text-orange-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Pending Requests</p>
                  <p className="text-2xl font-bold text-red-600">
                    {allRequests.filter(r => r.status === 'pending').length}
                  </p>
                </div>
                <Clock className="w-8 h-8 text-red-600" />
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Recent Activity */}
        <Card>
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {[...allRequests.slice(0, 3), ...allConcerns.slice(0, 3)]
                .sort((a, b) => new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime())
                .slice(0, 5)
                .map((item) => {
                  const user = getUserById(item.userId);
                  const isRequest = 'documentType' in item;
                  return (
                    <div key={`${isRequest ? 'req' : 'con'}-${item.id}`} className="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                      <div className={`p-2 rounded-full ${isRequest ? 'bg-blue-100' : 'bg-orange-100'}`}>
                        {isRequest ? 
                          <FileText className={`w-4 h-4 ${isRequest ? 'text-blue-600' : 'text-orange-600'}`} /> :
                          <MessageSquare className="w-4 h-4 text-orange-600" />
                        }
                      </div>
                      <div className="flex-1">
                        <p className="font-medium">
                          {isRequest ? 
                            `New document request: ${getDocumentTypeLabel((item as DocumentRequest).documentType)}` :
                            `New concern: ${(item as Concern).concernTitle}`
                          }
                        </p>
                        <p className="text-sm text-gray-600">
                          by {user?.fullName || 'Unknown User'} - {formatDate(item.submittedAt)}
                        </p>
                      </div>
                      <Badge className={getStatusBadgeColor(item.status)}>
                        {item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                      </Badge>
                    </div>
                  );
                })}
            </div>
          </CardContent>
        </Card>
      </div>
    </section>
  );

  const renderRequestsSection = () => (
    <section className="py-8 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold mb-2">Document Requests</h1>
            <p className="text-gray-600">Manage all user document requests</p>
          </div>
          <div className="flex items-center space-x-2 mt-4 md:mt-0">
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Search requests..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </div>

        {allRequests.length === 0 ? (
          <Card className="text-center py-12">
            <CardContent>
              <FileText className="w-16 h-16 mx-auto text-gray-400 mb-4" />
              <h3 className="text-lg font-semibold mb-2">No document requests</h3>
              <p className="text-gray-600">No document requests have been submitted yet.</p>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {allRequests
              .filter(request => {
                if (!searchTerm) return true;
                const user = getUserById(request.userId);
                return (
                  user?.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  user?.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  getDocumentTypeLabel(request.documentType).toLowerCase().includes(searchTerm.toLowerCase()) ||
                  request.purpose.toLowerCase().includes(searchTerm.toLowerCase())
                );
              })
              .map((request) => {
                const user = getUserById(request.userId);
                return (
                  <Card key={request.id} className="hover:shadow-md transition-shadow">
                    <CardContent className="p-6">
                      <div className="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                        <div>
                          <h3 className="text-lg font-semibold mb-1">
                            {getDocumentTypeLabel(request.documentType)}
                          </h3>
                          <p className="text-sm text-gray-600 mb-2">
                            Requested by: {user?.fullName || 'Unknown User'} ({user?.email || 'N/A'})
                          </p>
                        </div>
                        <div className="flex flex-col md:flex-row gap-2">
                          <Badge className={getStatusBadgeColor(request.status)}>
                            {request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                          </Badge>
                        </div>
                      </div>
                      
                      <div className="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                          <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Purpose</p>
                          <p className="text-sm text-gray-900">{request.purpose}</p>
                        </div>
                        <div>
                          <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Contact Number</p>
                          <p className="text-sm text-gray-900">{request.contactNumber}</p>
                        </div>
                        {request.additionalNotes && (
                          <div className="md:col-span-2">
                            <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Additional Notes</p>
                            <p className="text-sm text-gray-900">{request.additionalNotes}</p>
                          </div>
                        )}
                      </div>

                      <div className="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                        <div className="flex gap-2 flex-wrap">
                          <Button
                            size="sm"
                            variant={request.status === 'pending' ? 'default' : 'outline'}
                            onClick={() => updateRequestStatus(request.id, 'pending')}
                            disabled={request.status === 'pending'}
                          >
                            Pending
                          </Button>
                          <Button
                            size="sm"
                            variant={request.status === 'approved' ? 'default' : 'outline'}
                            onClick={() => updateRequestStatus(request.id, 'approved')}
                            disabled={request.status === 'approved'}
                          >
                            Approve
                          </Button>
                          <Button
                            size="sm"
                            variant={request.status === 'completed' ? 'default' : 'outline'}
                            onClick={() => updateRequestStatus(request.id, 'completed')}
                            disabled={request.status === 'completed'}
                          >
                            Complete
                          </Button>
                          <Button
                            size="sm"
                            variant={request.status === 'rejected' ? 'destructive' : 'outline'}
                            onClick={() => updateRequestStatus(request.id, 'rejected')}
                            disabled={request.status === 'rejected'}
                          >
                            Reject
                          </Button>
                        </div>
                        <div className="text-sm text-gray-500">
                          <span>Request ID: #{request.id}</span>
                          <span className="mx-2">•</span>
                          <span>Submitted: {formatDate(request.submittedAt)}</span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                );
              })}
          </div>
        )}
      </div>
    </section>
  );

  const renderConcernsSection = () => (
    <section className="py-8 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold mb-2">Community Concerns</h1>
            <p className="text-gray-600">Manage all user submitted concerns</p>
          </div>
          <div className="flex items-center space-x-2 mt-4 md:mt-0">
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Search concerns..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </div>

        {allConcerns.length === 0 ? (
          <Card className="text-center py-12">
            <CardContent>
              <MessageSquare className="w-16 h-16 mx-auto text-gray-400 mb-4" />
              <h3 className="text-lg font-semibold mb-2">No concerns submitted</h3>
              <p className="text-gray-600">No community concerns have been submitted yet.</p>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {allConcerns
              .filter(concern => {
                if (!searchTerm) return true;
                const user = getUserById(concern.userId);
                return (
                  user?.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  user?.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  concern.concernTitle.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  concern.concernDescription.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  getConcernTypeLabel(concern.concernType).toLowerCase().includes(searchTerm.toLowerCase())
                );
              })
              .map((concern) => {
                const user = getUserById(concern.userId);
                return (
                  <Card key={concern.id} className="hover:shadow-md transition-shadow">
                    <CardContent className="p-6">
                      <div className="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                        <div>
                          <h3 className="text-lg font-semibold mb-1">
                            {concern.concernTitle}
                          </h3>
                          <p className="text-sm text-gray-600 mb-2">
                            Submitted by: {user?.fullName || 'Unknown User'} ({user?.email || 'N/A'})
                          </p>
                        </div>
                        <div className="flex gap-2">
                          <Badge className={getUrgencyBadgeColor(concern.urgencyLevel)}>
                            {concern.urgencyLevel.charAt(0).toUpperCase() + concern.urgencyLevel.slice(1)}
                          </Badge>
                          <Badge className={getStatusBadgeColor(concern.status)}>
                            {concern.status.charAt(0).toUpperCase() + concern.status.slice(1)}
                          </Badge>
                        </div>
                      </div>
                      
                      <div className="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                          <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Type</p>
                          <p className="text-sm text-gray-900">{getConcernTypeLabel(concern.concernType)}</p>
                        </div>
                        {concern.concernLocation && (
                          <div>
                            <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Location</p>
                            <p className="text-sm text-gray-900">{concern.concernLocation}</p>
                          </div>
                        )}
                        <div className="md:col-span-2">
                          <p className="text-sm font-medium text-gray-500 uppercase tracking-wide">Description</p>
                          <p className="text-sm text-gray-900">{concern.concernDescription}</p>
                        </div>
                      </div>

                      <div className="flex flex-col md:flex-row md:items-center justify-between pt-4 border-t gap-4">
                        <div className="flex gap-2 flex-wrap">
                          <Button
                            size="sm"
                            variant={concern.status === 'submitted' ? 'default' : 'outline'}
                            onClick={() => updateConcernStatus(concern.id, 'submitted')}
                            disabled={concern.status === 'submitted'}
                          >
                            Submitted
                          </Button>
                          <Button
                            size="sm"
                            variant={concern.status === 'in-progress' ? 'default' : 'outline'}
                            onClick={() => updateConcernStatus(concern.id, 'in-progress')}
                            disabled={concern.status === 'in-progress'}
                          >
                            In Progress
                          </Button>
                          <Button
                            size="sm"
                            variant={concern.status === 'completed' ? 'default' : 'outline'}
                            onClick={() => updateConcernStatus(concern.id, 'completed')}
                            disabled={concern.status === 'completed'}
                          >
                            Complete
                          </Button>
                        </div>
                        <div className="text-sm text-gray-500">
                          <span>Concern ID: #{concern.id}</span>
                          <span className="mx-2">•</span>
                          <span>Submitted: {formatDate(concern.submittedAt)}</span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                );
              })}
          </div>
        )}
      </div>
    </section>
  );

  const renderUsersSection = () => (
    <section className="py-8 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold mb-2">Registered Users</h1>
            <p className="text-gray-600">View all registered barangay users</p>
          </div>
          <div className="flex items-center space-x-2 mt-4 md:mt-0">
            <div className="relative">
              <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Search users..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </div>

        {allUsers.length === 0 ? (
          <Card className="text-center py-12">
            <CardContent>
              <Users className="w-16 h-16 mx-auto text-gray-400 mb-4" />
              <h3 className="text-lg font-semibold mb-2">No users registered</h3>
              <p className="text-gray-600">No users have registered yet.</p>
            </CardContent>
          </Card>
        ) : (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {allUsers
              .filter(user => {
                if (!searchTerm) return true;
                return (
                  user.fullName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                  user.email.toLowerCase().includes(searchTerm.toLowerCase())
                );
              })
              .map((user) => {
                const userRequests = allRequests.filter(req => req.userId === user.id);
                const userConcerns = allConcerns.filter(con => con.userId === user.id);
                
                return (
                  <Card key={user.id} className="hover:shadow-md transition-shadow">
                    <CardContent className="p-6">
                      <div className="flex items-center space-x-3 mb-4">
                        <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                          <UserCheck className="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                          <h3 className="text-lg font-semibold">{user.fullName}</h3>
                          <p className="text-sm text-gray-600">{user.email}</p>
                        </div>
                      </div>
                      
                      <div className="grid grid-cols-2 gap-4 mb-4">
                        <div className="text-center p-3 bg-gray-50 rounded-lg">
                          <div className="text-lg font-bold text-blue-600">{userRequests.length}</div>
                          <div className="text-xs text-gray-600">Requests</div>
                        </div>
                        <div className="text-center p-3 bg-gray-50 rounded-lg">
                          <div className="text-lg font-bold text-orange-600">{userConcerns.length}</div>
                          <div className="text-xs text-gray-600">Concerns</div>
                        </div>
                      </div>
                      
                      <div className="text-sm text-gray-500 text-center">
                        Joined: {formatDate(user.createdAt)}
                      </div>
                    </CardContent>
                  </Card>
                );
              })}
          </div>
        )}
      </div>
    </section>
  );

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation */}
      <nav className="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-2">
              <Link className="h-8 w-8" />
              <span className="text-xl font-semibold">BarangayLink Admin</span>
            </div>
            
            {/* Desktop Menu */}
            <div className="hidden md:flex items-center space-x-8">
              <button 
                onClick={() => handleSectionChange('overview')}
                className={`hover:bg-white/10 px-3 py-2 rounded-md transition-colors ${currentSection === 'overview' ? 'bg-white/10' : ''}`}
              >
                Dashboard
              </button>
              <button 
                onClick={() => handleSectionChange('requests')}
                className={`hover:bg-white/10 px-3 py-2 rounded-md transition-colors ${currentSection === 'requests' ? 'bg-white/10' : ''}`}
              >
                Document Requests
              </button>
              <button 
                onClick={() => handleSectionChange('concerns')}
                className={`hover:bg-white/10 px-3 py-2 rounded-md transition-colors ${currentSection === 'concerns' ? 'bg-white/10' : ''}`}
              >
                Concerns
              </button>
              <button 
                onClick={() => handleSectionChange('users')}
                className={`hover:bg-white/10 px-3 py-2 rounded-md transition-colors ${currentSection === 'users' ? 'bg-white/10' : ''}`}
              >
                Users
              </button>
              <Button 
                onClick={handleLogout}
                variant="ghost" 
                className="text-white hover:bg-white/10"
              >
                <LogOut className="w-4 h-4 mr-2" />
                Logout
              </Button>
            </div>

            {/* User Profile */}
            <div className="flex items-center space-x-3">
              <div className="hidden md:flex items-center space-x-2">
                <Settings className="w-5 h-5" />
                <span>Admin: {user?.fullName.split(' ')[0]}</span>
              </div>
              
              {/* Mobile Menu Button */}
              <div className="md:hidden">
                <button
                  onClick={() => setIsMenuOpen(!isMenuOpen)}
                  className="p-2 rounded-md hover:bg-white/10"
                >
                  <div className="w-6 h-6 flex flex-col justify-around">
                    <span className={`h-0.5 w-6 bg-white transform transition ${isMenuOpen ? 'rotate-45 translate-y-2.5' : ''}`} />
                    <span className={`h-0.5 w-6 bg-white transition ${isMenuOpen ? 'opacity-0' : ''}`} />
                    <span className={`h-0.5 w-6 bg-white transform transition ${isMenuOpen ? '-rotate-45 -translate-y-2.5' : ''}`} />
                  </div>
                </button>
              </div>
            </div>
          </div>

          {/* Mobile Menu */}
          {isMenuOpen && (
            <div className="md:hidden bg-blue-700 border-t border-blue-600">
              <div className="px-2 pt-2 pb-3 space-y-1">
                <button 
                  onClick={() => handleSectionChange('overview')}
                  className="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left"
                >
                  Dashboard
                </button>
                <button 
                  onClick={() => handleSectionChange('requests')}
                  className="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left"
                >
                  Document Requests
                </button>
                <button 
                  onClick={() => handleSectionChange('concerns')}
                  className="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left"
                >
                  Concerns
                </button>
                <button 
                  onClick={() => handleSectionChange('users')}
                  className="block px-3 py-2 hover:bg-white/10 rounded-md w-full text-left"
                >
                  Users
                </button>
                <Button 
                  onClick={handleLogout}
                  variant="ghost" 
                  className="w-full justify-start text-white hover:bg-white/10 mt-2"
                >
                  <LogOut className="w-4 h-4 mr-2" />
                  Logout
                </Button>
              </div>
            </div>
          )}
        </div>
      </nav>

      {/* Section Content */}
      {currentSection === 'overview' && renderOverviewSection()}
      {currentSection === 'requests' && renderRequestsSection()}
      {currentSection === 'concerns' && renderConcernsSection()}
      {currentSection === 'users' && renderUsersSection()}
    </div>
  );
}