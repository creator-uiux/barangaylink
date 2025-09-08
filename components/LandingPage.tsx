import { useState } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { Badge } from './ui/badge';
import { AuthModal } from './AuthModal';
import { ContactForm } from './ContactForm';
import { Link, Users, Calendar, FileText, CheckCircle, Building, Phone, Mail, Clock, MapPin } from 'lucide-react';

interface LandingPageProps {
  onNavigateToDashboard: () => void;
}

export function LandingPage({ onNavigateToDashboard }: LandingPageProps) {
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    element?.scrollIntoView({ behavior: 'smooth' });
    setIsMenuOpen(false);
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Navigation */}
      <nav className="fixed top-0 w-full bg-background/80 backdrop-blur-md z-50 border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-2">
              <Link className="h-8 w-8 text-primary" />
              <span className="text-xl font-semibold text-primary">BarangayLink</span>
            </div>
            
            {/* Desktop Menu */}
            <div className="hidden md:flex items-center space-x-8">
              <button onClick={() => scrollToSection('home')} className="hover:text-primary transition-colors">
                Home
              </button>
              <button onClick={() => scrollToSection('announcements')} className="hover:text-primary transition-colors">
                Announcements
              </button>
              <button onClick={() => scrollToSection('events')} className="hover:text-primary transition-colors">
                Events
              </button>
              <button onClick={() => scrollToSection('projects')} className="hover:text-primary transition-colors">
                Projects
              </button>
              <button onClick={() => scrollToSection('contact')} className="hover:text-primary transition-colors">
                Contact
              </button>
              <Button onClick={() => setIsAuthModalOpen(true)}>
                Login / Signup
              </Button>
            </div>

            {/* Mobile Menu Button */}
            <div className="md:hidden">
              <button
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                className="p-2 rounded-md hover:bg-gray-100"
              >
                <div className="w-6 h-6 flex flex-col justify-around">
                  <span className={`h-0.5 w-6 bg-gray-600 transform transition ${isMenuOpen ? 'rotate-45 translate-y-2.5' : ''}`} />
                  <span className={`h-0.5 w-6 bg-gray-600 transition ${isMenuOpen ? 'opacity-0' : ''}`} />
                  <span className={`h-0.5 w-6 bg-gray-600 transform transition ${isMenuOpen ? '-rotate-45 -translate-y-2.5' : ''}`} />
                </div>
              </button>
            </div>
          </div>

          {/* Mobile Menu */}
          {isMenuOpen && (
            <div className="md:hidden bg-background border-t">
              <div className="px-2 pt-2 pb-3 space-y-1">
                <button onClick={() => scrollToSection('home')} className="block px-3 py-2 w-full text-left hover:bg-gray-100 rounded-md">
                  Home
                </button>
                <button onClick={() => scrollToSection('announcements')} className="block px-3 py-2 w-full text-left hover:bg-gray-100 rounded-md">
                  Announcements
                </button>
                <button onClick={() => scrollToSection('events')} className="block px-3 py-2 w-full text-left hover:bg-gray-100 rounded-md">
                  Events
                </button>
                <button onClick={() => scrollToSection('projects')} className="block px-3 py-2 w-full text-left hover:bg-gray-100 rounded-md">
                  Projects
                </button>
                <button onClick={() => scrollToSection('contact')} className="block px-3 py-2 w-full text-left hover:bg-gray-100 rounded-md">
                  Contact
                </button>
                <Button onClick={() => setIsAuthModalOpen(true)} className="w-full mt-2">
                  Login / Signup
                </Button>
              </div>
            </div>
          )}
        </div>
      </nav>

      {/* Hero Section */}
      <section id="home" className="pt-16 min-h-screen flex items-center">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="text-center lg:text-left">
              <h1 className="text-4xl lg:text-6xl font-bold text-gray-900 mb-6">
                BarangayLink - Your Direct Link to Local Updates and Services
              </h1>
              <p className="text-xl text-gray-600 mb-8 max-w-2xl">
                Stay connected with your barangay community. Get real-time updates, access services, and participate in local governance.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <Button 
                  onClick={() => setIsAuthModalOpen(true)} 
                  size="lg"
                >
                  Get Started
                </Button>
                <Button 
                  onClick={() => scrollToSection('announcements')} 
                  variant="outline" 
                  size="lg"
                >
                  View Announcements
                </Button>
              </div>
            </div>
            <div className="flex justify-center lg:justify-end">
              <div className="w-72 h-72 bg-blue-100 rounded-full flex items-center justify-center">
                <Users className="w-32 h-32 text-blue-600" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Announcements Preview */}
      <section id="announcements" className="py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-center mb-12">Latest Announcements</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card className="p-6 hover:shadow-lg transition-shadow border-l-4 border-l-primary">
              <div className="flex justify-between items-start mb-4">
                <div className="p-2 bg-primary/10 rounded-lg">
                  <Users className="w-5 h-5 text-primary" />
                </div>
                <Badge variant="secondary">Dec 15, 2024</Badge>
              </div>
              <h3 className="text-lg font-semibold mb-3">Community Clean-up Drive</h3>
              <p className="text-gray-600 mb-4">
                Join us for our monthly community clean-up drive this Saturday at 7:00 AM. Let's keep our barangay clean and green!
              </p>
              <Button variant="link" className="p-0 text-primary">Read More →</Button>
            </Card>

            <Card className="p-6 hover:shadow-lg transition-shadow border-l-4 border-l-primary">
              <div className="flex justify-between items-start mb-4">
                <div className="p-2 bg-primary/10 rounded-lg">
                  <Calendar className="w-5 h-5 text-primary" />
                </div>
                <Badge variant="secondary">Dec 12, 2024</Badge>
              </div>
              <h3 className="text-lg font-semibold mb-3">Barangay Assembly Meeting</h3>
              <p className="text-gray-600 mb-4">
                Monthly barangay assembly meeting scheduled for December 20, 2024. All residents are encouraged to attend.
              </p>
              <Button variant="link" className="p-0 text-primary">Read More →</Button>
            </Card>

            <Card className="p-6 hover:shadow-lg transition-shadow border-l-4 border-l-primary">
              <div className="flex justify-between items-start mb-4">
                <div className="p-2 bg-primary/10 rounded-lg">
                  <FileText className="w-5 h-5 text-primary" />
                </div>
                <Badge variant="secondary">Dec 10, 2024</Badge>
              </div>
              <h3 className="text-lg font-semibold mb-3">Free Medical Check-up</h3>
              <p className="text-gray-600 mb-4">
                Free medical check-up and consultation available at the barangay health center every Tuesday and Thursday.
              </p>
              <Button variant="link" className="p-0 text-blue-600">Read More →</Button>
            </Card>
          </div>
        </div>
      </section>

      {/* Events & Projects Timeline */}
      <section id="events" className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-center mb-12">Events & Projects</h2>
          <div className="max-w-4xl mx-auto">
            <div className="relative">
              <div className="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-600"></div>
              
              <div className="relative flex justify-end mb-8">
                <div className="w-5/12 bg-white p-6 rounded-lg shadow-md mr-8">
                  <h3 className="text-lg font-semibold mb-2">Christmas Festival</h3>
                  <p className="text-gray-600">Annual Christmas celebration with cultural performances, food stalls, and activities for the whole family.</p>
                </div>
                <div className="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                  Dec 2024
                </div>
              </div>

              <div className="relative flex justify-start mb-8">
                <div className="w-5/12 bg-white p-6 rounded-lg shadow-md ml-8">
                  <h3 className="text-lg font-semibold mb-2">Road Improvement Project</h3>
                  <p className="text-gray-600">Major road repairs and improvements completed on Main Street and surrounding areas.</p>
                </div>
                <div className="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                  Nov 2024
                </div>
              </div>

              <div className="relative flex justify-end">
                <div className="w-5/12 bg-white p-6 rounded-lg shadow-md mr-8">
                  <h3 className="text-lg font-semibold mb-2">Youth Skills Training</h3>
                  <p className="text-gray-600">Computer literacy and vocational training program for barangay youth aged 16-25.</p>
                </div>
                <div className="absolute left-1/2 transform -translate-x-1/2 -translate-y-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                  Oct 2024
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section className="py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-center mb-12">How It Works</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="relative inline-block mb-6">
                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                  <Users className="w-8 h-8 text-white" />
                </div>
                <div className="absolute -top-2 -right-2 w-8 h-8 bg-blue-800 text-white rounded-full flex items-center justify-center font-bold text-sm">
                  1
                </div>
              </div>
              <h3 className="text-lg font-semibold mb-3">Register</h3>
              <p className="text-gray-600">Create your account with basic information to get started with BarangayLink services.</p>
            </div>

            <div className="text-center">
              <div className="relative inline-block mb-6">
                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                  <CheckCircle className="w-8 h-8 text-white" />
                </div>
                <div className="absolute -top-2 -right-2 w-8 h-8 bg-blue-800 text-white rounded-full flex items-center justify-center font-bold text-sm">
                  2
                </div>
              </div>
              <h3 className="text-lg font-semibold mb-3">Login</h3>
              <p className="text-gray-600">Access your personalized dashboard with your registered credentials.</p>
            </div>

            <div className="text-center">
              <div className="relative inline-block mb-6">
                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                  <Calendar className="w-8 h-8 text-white" />
                </div>
                <div className="absolute -top-2 -right-2 w-8 h-8 bg-blue-800 text-white rounded-full flex items-center justify-center font-bold text-sm">
                  3
                </div>
              </div>
              <h3 className="text-lg font-semibold mb-3">Stay Updated</h3>
              <p className="text-gray-600">Receive real-time notifications about announcements, events, and important updates.</p>
            </div>

            <div className="text-center">
              <div className="relative inline-block mb-6">
                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                  <FileText className="w-8 h-8 text-white" />
                </div>
                <div className="absolute -top-2 -right-2 w-8 h-8 bg-blue-800 text-white rounded-full flex items-center justify-center font-bold text-sm">
                  4
                </div>
              </div>
              <h3 className="text-lg font-semibold mb-3">Request Documents</h3>
              <p className="text-gray-600">Submit requests for barangay certificates and other official documents online.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section id="contact" className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-center mb-12">Contact Us</h2>
          <div className="grid lg:grid-cols-2 gap-12">
            <div>
              <h3 className="text-xl font-semibold mb-6">Get in Touch</h3>
              <div className="space-y-4">
                <div className="flex items-start space-x-3">
                  <MapPin className="w-5 h-5 text-blue-600 mt-1" />
                  <div>
                    <p className="text-gray-900">Barangay Hall, Main Street</p>
                    <p className="text-gray-600">Your City, Philippines</p>
                  </div>
                </div>
                <div className="flex items-center space-x-3">
                  <Phone className="w-5 h-5 text-blue-600" />
                  <p className="text-gray-600">+63 (02) 123-4567</p>
                </div>
                <div className="flex items-center space-x-3">
                  <Mail className="w-5 h-5 text-blue-600" />
                  <p className="text-gray-600">info@barangaylink.com</p>
                </div>
                <div className="flex items-center space-x-3">
                  <Clock className="w-5 h-5 text-blue-600" />
                  <p className="text-gray-600">Monday - Friday: 8:00 AM - 5:00 PM</p>
                </div>
              </div>
            </div>
            <ContactForm />
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-900 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
              <div className="flex items-center space-x-2 mb-4">
                <Link className="h-6 w-6" />
                <span className="text-lg font-semibold">BarangayLink</span>
              </div>
              <p className="text-gray-400 mb-4">
                Connecting communities, empowering residents, and promoting transparency in local governance.
              </p>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Quick Links</h4>
              <ul className="space-y-2 text-gray-400">
                <li><button onClick={() => scrollToSection('home')} className="hover:text-white transition-colors">Home</button></li>
                <li><button onClick={() => scrollToSection('announcements')} className="hover:text-white transition-colors">Announcements</button></li>
                <li><button onClick={() => scrollToSection('events')} className="hover:text-white transition-colors">Events</button></li>
                <li><button onClick={() => scrollToSection('contact')} className="hover:text-white transition-colors">Contact</button></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Services</h4>
              <ul className="space-y-2 text-gray-400">
                <li><a href="#" className="hover:text-white transition-colors">Document Requests</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Community Reports</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Online Services</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Support</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Barangay Info</h4>
              <div className="text-gray-400 space-y-1">
                <p><span className="font-medium">Captain:</span> Juan Dela Cruz</p>
                <p><span className="font-medium">Population:</span> 15,000</p>
                <p><span className="font-medium">Area:</span> 2.5 sq km</p>
              </div>
            </div>
          </div>
          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 BarangayLink. All rights reserved.</p>
          </div>
        </div>
      </footer>

      <AuthModal 
        isOpen={isAuthModalOpen} 
        onClose={() => setIsAuthModalOpen(false)}
        onSuccess={onNavigateToDashboard}
      />
    </div>
  );
}