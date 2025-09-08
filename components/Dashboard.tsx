import { useState } from 'react';
import { Button } from './ui/button';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Badge } from './ui/badge';
import { useAuth } from './AuthContext';
import { DocumentRequestModal } from './DocumentRequestModal';
import { ConcernModal } from './ConcernModal';
import { 
  Link, 
  Users, 
  Calendar, 
  FileText, 
  MessageSquare, 
  Settings, 
  LogOut,
  Sun,
  Hammer,
  CheckCircle,
  Clock,
  Bell
} from 'lucide-react';
import { toast } from 'sonner@2.0.3';

interface DashboardProps {
  onNavigateToLanding: () => void;
}

export function Dashboard({ onNavigateToLanding }: DashboardProps) {
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isDocumentModalOpen, setIsDocumentModalOpen] = useState(false);
  const [isConcernModalOpen, setIsConcernModalOpen] = useState(false);

  const handleLogout = () => {
    logout();
    toast.success('Logged out successfully');
    onNavigateToLanding();
  };

  const handleQuickAction = (action: string) => {
    switch (action) {
      case 'announcements':
        toast.info('Redirecting to announcements...');
        break;
      case 'events':
        toast.info('Redirecting to events...');
        break;
      case 'projects':
        toast.info('Redirecting to projects...');
        break;
      case 'documents':
        setIsDocumentModalOpen(true);
        break;
      case 'concerns':
        setIsConcernModalOpen(true);
        break;
      case 'profile':
        toast.info('Redirecting to profile settings...');
        break;
      default:
        toast.info('Feature coming soon!');
    }
  };

  const quickAccessItems = [
    {
      id: 'announcements',
      title: 'Announcements',
      description: 'View latest barangay announcements and updates',
      icon: Users,
      badge: '3 New',
      badgeColor: 'bg-red-500'
    },
    {
      id: 'events',
      title: 'Events',
      description: 'Check upcoming community events and activities',
      icon: Calendar,
      badge: '2 Upcoming',
      badgeColor: 'bg-blue-500'
    },
    {
      id: 'projects',
      title: 'Projects',
      description: 'Track ongoing barangay development projects',
      icon: Hammer,
      badge: null,
      badgeColor: ''
    },
    {
      id: 'documents',
      title: 'Request Documents',
      description: 'Apply for barangay certificates and clearances',
      icon: FileText,
      badge: null,
      badgeColor: ''
    },
    {
      id: 'concerns',
      title: 'Submit Concerns',
      description: 'Report issues or submit feedback to barangay officials',
      icon: MessageSquare,
      badge: null,
      badgeColor: ''
    },
    {
      id: 'profile',
      title: 'My Profile',
      description: 'Update your personal information and settings',
      icon: Settings,
      badge: null,
      badgeColor: ''
    }
  ];

  const recentUpdates = [
    {
      id: 1,
      type: 'announcement',
      icon: Users,
      title: 'New Announcement: Community Clean-up Drive',
      description: 'Join us for our monthly community clean-up drive this Saturday at 7:00 AM.',
      time: '2 hours ago'
    },
    {
      id: 2,
      type: 'event',
      icon: Calendar,
      title: 'Event Reminder: Barangay Assembly Meeting',
      description: 'Monthly barangay assembly meeting scheduled for December 20, 2024.',
      time: '1 day ago'
    },
    {
      id: 3,
      type: 'document',
      icon: CheckCircle,
      title: 'Document Request Approved',
      description: 'Your barangay clearance request has been approved and is ready for pickup.',
      time: '2 days ago'
    },
    {
      id: 4,
      type: 'project',
      icon: Hammer,
      title: 'Project Update: Road Improvement',
      description: 'Phase 2 of the road improvement project on Main Street has been completed.',
      time: '3 days ago'
    },
    {
      id: 5,
      type: 'health',
      icon: FileText,
      title: 'Health Service: Free Medical Check-up',
      description: 'Free medical check-up available at the barangay health center every Tuesday and Thursday.',
      time: '1 week ago'
    }
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation */}
      <nav className="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-2">
              <Link className="h-8 w-8" />
              <span className="text-xl font-semibold">BarangayLink</span>
            </div>
            
            {/* Desktop Menu */}
            <div className="hidden md:flex items-center space-x-8">
              <a href="#" className="hover:bg-white/10 px-3 py-2 rounded-md transition-colors">Dashboard</a>
              <a href="#" className="hover:bg-white/10 px-3 py-2 rounded-md transition-colors">Announcements</a>
              <a href="#" className="hover:bg-white/10 px-3 py-2 rounded-md transition-colors">Events</a>
              <a href="#" className="hover:bg-white/10 px-3 py-2 rounded-md transition-colors">Projects</a>
              <a href="#" className="hover:bg-white/10 px-3 py-2 rounded-md transition-colors">My Requests</a>
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
                <span>Welcome, {user?.fullName.split(' ')[0]}</span>
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
                <a href="#" className="block px-3 py-2 hover:bg-white/10 rounded-md">Dashboard</a>
                <a href="#" className="block px-3 py-2 hover:bg-white/10 rounded-md">Announcements</a>
                <a href="#" className="block px-3 py-2 hover:bg-white/10 rounded-md">Events</a>
                <a href="#" className="block px-3 py-2 hover:bg-white/10 rounded-md">Projects</a>
                <a href="#" className="block px-3 py-2 hover:bg-white/10 rounded-md">My Requests</a>
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

      {/* Welcome Banner */}
      <section className="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col lg:flex-row justify-between items-center">
            <div className="text-center lg:text-left mb-6 lg:mb-0">
              <h1 className="text-3xl font-bold mb-2">Welcome, {user?.fullName.split(' ')[0]}!</h1>
              <p className="text-blue-100 mb-4">Stay up to date with the latest barangay news, events, and services.</p>
              <div className="flex items-center justify-center lg:justify-start space-x-2">
                <Sun className="w-5 h-5" />
                <span className="text-blue-100">Today: Sunny, 28°C</span>
              </div>
            </div>
            <div className="flex space-x-8 text-center">
              <div className="bg-white/10 p-4 rounded-lg">
                <div className="text-2xl font-bold">3</div>
                <div className="text-sm text-blue-100">New Announcements</div>
              </div>
              <div className="bg-white/10 p-4 rounded-lg">
                <div className="text-2xl font-bold">2</div>
                <div className="text-sm text-blue-100">Upcoming Events</div>
              </div>
              <div className="bg-white/10 p-4 rounded-lg">
                <div className="text-2xl font-bold">1</div>
                <div className="text-sm text-blue-100">Pending Requests</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Quick Access Cards */}
      <section className="py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold mb-8">Quick Access</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {quickAccessItems.map((item) => {
              const IconComponent = item.icon;
              return (
                <Card 
                  key={item.id}
                  className="cursor-pointer hover:shadow-lg transition-all duration-200 hover:-translate-y-1 border-l-4 border-l-blue-600 relative"
                  onClick={() => handleQuickAction(item.id)}
                >
                  {item.badge && (
                    <div className={`absolute top-3 right-3 ${item.badgeColor} text-white text-xs px-2 py-1 rounded-full`}>
                      {item.badge}
                    </div>
                  )}
                  <CardHeader className="pb-3">
                    <div className="flex items-center space-x-3">
                      <div className="p-2 bg-blue-100 rounded-lg">
                        <IconComponent className="w-6 h-6 text-blue-600" />
                      </div>
                      <CardTitle className="text-lg">{item.title}</CardTitle>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <p className="text-gray-600 text-sm">{item.description}</p>
                  </CardContent>
                </Card>
              );
            })}
          </div>
        </div>
      </section>

      {/* Recent Updates */}
      <section className="pb-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold mb-8">Recent Updates</h2>
          <Card>
            <CardContent className="p-0">
              <div className="divide-y">
                {recentUpdates.map((update) => {
                  const IconComponent = update.icon;
                  return (
                    <div key={update.id} className="p-6 hover:bg-gray-50 transition-colors">
                      <div className="flex space-x-4">
                        <div className="flex-shrink-0">
                          <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <IconComponent className="w-6 h-6 text-blue-600" />
                          </div>
                        </div>
                        <div className="flex-1">
                          <h4 className="font-semibold text-gray-900 mb-1">{update.title}</h4>
                          <p className="text-gray-600 mb-2">{update.description}</p>
                          <div className="flex items-center text-sm text-gray-500">
                            <Clock className="w-4 h-4 mr-1" />
                            {update.time}
                          </div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </CardContent>
          </Card>
          <div className="text-center mt-6">
            <Button variant="outline" onClick={() => toast.info('Loading more updates...')}>
              Load More Updates
            </Button>
          </div>
        </div>
      </section>

      {/* Modals */}
      <DocumentRequestModal 
        isOpen={isDocumentModalOpen}
        onClose={() => setIsDocumentModalOpen(false)}
      />
      
      <ConcernModal 
        isOpen={isConcernModalOpen}
        onClose={() => setIsConcernModalOpen(false)}
      />
    </div>
  );
}