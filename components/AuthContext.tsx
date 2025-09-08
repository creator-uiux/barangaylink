import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';

interface User {
  id: number;
  fullName: string;
  email: string;
  createdAt: string;
}

interface AuthContextType {
  user: User | null;
  isLoading: boolean;
  login: (email: string, password: string, rememberMe?: boolean) => Promise<{ success: boolean; message: string }>;
  signup: (fullName: string, email: string, password: string) => Promise<{ success: boolean; message: string }>;
  logout: () => void;
  resetPassword: (email: string) => Promise<{ success: boolean; message: string }>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Check for stored user session on component mount
    const storedUser = localStorage.getItem('barangaylink_current_user') || 
                      sessionStorage.getItem('barangaylink_current_user');
    
    if (storedUser) {
      try {
        setUser(JSON.parse(storedUser));
      } catch (error) {
        console.error('Error parsing stored user:', error);
      }
    }
    setIsLoading(false);
  }, []);

  const getUsers = (): User[] => {
    try {
      return JSON.parse(localStorage.getItem('barangaylink_users') || '[]');
    } catch {
      return [];
    }
  };

  const saveUsers = (users: User[]) => {
    localStorage.setItem('barangaylink_users', JSON.stringify(users));
  };

  const login = async (email: string, password: string, rememberMe = false): Promise<{ success: boolean; message: string }> => {
    const users = getUsers();
    const foundUser = users.find(u => u.email === email && u.password === password);

    if (!foundUser) {
      return {
        success: false,
        message: 'Invalid email or password. Please sign up if you don\'t have an account.'
      };
    }

    const userToStore = {
      id: foundUser.id,
      fullName: foundUser.fullName,
      email: foundUser.email,
      createdAt: foundUser.createdAt
    };

    setUser(userToStore);

    if (rememberMe) {
      localStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
    } else {
      sessionStorage.setItem('barangaylink_current_user', JSON.stringify(userToStore));
    }

    return { success: true, message: 'Login successful!' };
  };

  const signup = async (fullName: string, email: string, password: string): Promise<{ success: boolean; message: string }> => {
    const users = getUsers();

    if (users.find(u => u.email === email)) {
      return {
        success: false,
        message: 'An account with this email already exists'
      };
    }

    const newUser = {
      id: Date.now(),
      fullName,
      email,
      password,
      createdAt: new Date().toISOString()
    };

    users.push(newUser);
    saveUsers(users);

    return { success: true, message: 'Account created successfully! Please login.' };
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('barangaylink_current_user');
    sessionStorage.removeItem('barangaylink_current_user');
  };

  const resetPassword = async (email: string): Promise<{ success: boolean; message: string }> => {
    const users = getUsers();
    const userExists = users.find(u => u.email === email);

    if (!userExists) {
      return {
        success: false,
        message: 'No account found with this email address. Please sign up first.'
      };
    }

    return {
      success: true,
      message: 'Password reset instructions have been sent to your email address.'
    };
  };

  const value = {
    user,
    isLoading,
    login,
    signup,
    logout,
    resetPassword
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}